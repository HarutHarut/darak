<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\SetNewPasswordRequest;
use App\Luglocker\Email\EmailCreator;
use App\Models\Order;
use DateTimeZone;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Nette\Utils\DateTime;
use stdClass;
use \Throwable;
use App\Models\User;
use Mockery\Exception;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Utilities\ProxyRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Api\Auth\Login;
use Illuminate\Support\Facades\Config;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\Auth\Register;

class AuthController extends ApiController
{
    protected $proxy;

    public function __construct(ProxyRequest $proxy)
    {
        $this->proxy = $proxy;
    }

    public function register(Register $request)
    {
        $data = $request->validated();

        try {
            DB::beginTransaction();

            $user = User::query()->create([
                'name' => $data['name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'currency' => "EUR",
                'role_id' => isset($data['business_name']) ? 3 : 2,
                'password' => Hash::make($data['password']),
            ]);
            if (isset($data['business_name'])) {
                $name = new stdClass();
                $name->ru = null;
                $name->sp = null;
                $name->ch = null;
                $name->am = null;
                $name->de = null;
                $name->fr = null;
                $name->en = $data['business_name'];
                $data['business_name'] = $name;

                $country_code = json_encode(array('country' => $data['phone_country'], 'code' => $data['phone_code']));

                Business::query()->create([
                    'user_id' => $user->id,
                    'name' => $data['business_name'],
                    'email' => $data['email'],
                    'phone' => $data['phone'],
                    'country_code' => $country_code,
                    'timezone' => $data['timezone'],
                    'currency' => $data['currency'] ?? 'EUR',
                ]);
            }

            $this->generateVerificationUrl($user);

            DB::commit();
            return $this->success(201, [
                'user' => $user
            ], __("general.verifyYourEmail"));

        } catch (Exception $e) {
            DB::rollback();
            $this->errorLog($request, $e, 'AuthController register action');
            return $this->error($e->getCode(), $e->getMessage());
        } catch (\Throwable $e) {
            DB::rollback();
            $this->errorLog($request, $e, 'AuthController register action');
            return $this->error(400, "Register failed.");
        }
    }

    public function login(Login $request): JsonResponse
    {
        $data = $request->validated();

        $user = User::query()
            ->where('email', '=', $data['email'])
            ->first();
        $firstLogin = false;
        try {
            if (!$user) {
                throw new Exception('This combination does not exists.', 403);
            }

            if (!$user->hasVerifiedEmail()) {
                throw new Exception('Your email address is not verified.', 403);
            }

            if (!Hash::check($data['password'], $user->password)) {
                throw new Exception('This combination does not exists.', 403);
            }

            if ($user->status == config('constants.user_status.blocked')) {
                throw new Exception('Your account has been blocked by admin.', 403);
            }

            $orders = Order::with('feedback')
                ->where('user_id', $user['id'])
                ->get();

            if ($user->first_login == null){
                $user->first_login = Carbon::now();
                $user->save();
                $firstLogin = true;
            }
            $user['firstLogin'] = $firstLogin;

            $resp = $this->proxy
                ->grantPasswordToken($data['email'], $data['password']);

            return $this->success(200, [
                'access_token' => $resp->access_token,
                'expires_in' => $resp->expires_in,
                'refresh_token' => $resp->refresh_token,
                "user" => $user,
                "orders" => $orders
            ], 'You have been logged in.');

        } catch (Exception $e) {
            DB::rollback();
            return $this->error($e->getCode(), $e->getMessage());
        } catch (\Throwable $e) {
            DB::rollback();
            $this->errorLog($request, $e, 'AuthController login action', $user->id);
            return $this->error(400, $e->getMessage());
        }

    }

    /**
     * @param Request $request
     * @param $provider
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function socLogin(Request $request, $provider){
        $access_token = '';
        $data = $request->all();
        if (isset($data['#access_token'])){
            $access_token = $data['#access_token'];
        }

        $url = "https://graph.facebook.com/v6.0/me?fields=id,name,picture{url},email&access_token=" . $access_token;
        $client = new Client();
        $response = $client->get($url);

        $response = json_decode($response->getBody()->getContents());

        try {
            $userEmail = User::with('role')->where('email', $response->email)->first();
            if($userEmail){
                $user = $userEmail;
            }

            $userProviderId = User::with('role')->where('provider_id', $response->id)->first();
            if($userProviderId){
                $user = $userProviderId;
//                return response()->json($userProviderId);
            }

            if(!$userEmail && !$userProviderId){
                $user = User::with('role')->create([
                    'name' => $response->name,
                    'email' => $response->email,
                    'password' => bcrypt('password'),
                    'avatar' => $response->picture->data->url,
                    'status' => config('constants.user_status.verified'),
                    "role_id" => 2,
                    'email_verified_at' => now(),
                    'provider' => $provider,
                    'provider_id' => $response->id
                ]);
            }
            $orders = Order::with('feedback')
                ->where('user_id', $user['id'])
                ->get();

            $resp = $this->proxy
                ->grantPasswordToken($response->email, "password");

            return $this->success(200, [
                'access_token' => $resp->access_token,
                'expires_in' => $resp->expires_in,
                'refresh_token' => $resp->refresh_token,
                "user" => $user,
                "orders" => $orders,
            ], 'You have been logged in.');

        } catch (\Throwable $e) {
            $this->errorLog($request, $e, 'AuthController provider register action');
            return $this->error(400, "Register failed.");
        }

    }

    public function user(Request $request): JsonResponse
    {
        $user = User::query()
            ->with('role')
            ->find(Auth::id());

        return response()->json($user);
    }

    public function refreshToken(Request $request): JsonResponse
    {
        $resp = $this->proxy->refreshAccessToken();

        return $this->success(200, [
            'access_token' => $resp->access_token,
            'expires_in' => $resp->expires_in,
            'refresh_token' => $resp->refresh_token,
        ], 'Token has been refreshed.');
    }

    public function logout(Request $request): JsonResponse
    {
        $token = request()->user()->token();
        $token->delete();

        // remove the httponly cookie
        cookie()->queue(cookie()->forget('refresh_token'));

        return $this->success(200, [], 'You have been successfully logged out.');
    }

    /**
     * @param User $user
     * @return string
     */
    private function generateVerificationUrl(User $user)
    {
        $hash = $this->getResetPasswordHash($user);
//        $image = config('app.beck_url') . '/img/logo-square.jpg';
        $viewData = [
            'subject' => __('general.emails.UserRegistered.subject'),
            'email' => $user->email,
            'url' => $hash,
//            'image' => $image
        ];

        EmailCreator::create(
            $user->id,
            $user->email,
            $viewData['subject'],
            view('emails.UserRegistered', $viewData)->render(),
            'emails.UserRegistered',
            config('constants.email_type.account_verify')
        );


    }

    public function emailVerify(Request $request){

        $data = Crypt::decrypt($request->post('hash'));
        try {

            $user = User::query()->find($data['user_id']);
            $user->email_verified_at = date('Y-m-d h:i:s');
            $user->status = config('constants.user_status.verified');
            $user->save();

            $viewData = [
                'subject' => __('general.emails.VerifyAccount.subject'),
                'email' => $user->email,
            ];

            EmailCreator::create(
                $user->id,
                $user->email,
                $viewData['subject'],
                view('emails.VerifyAccount', $viewData)->render(),
                'emails.VerifyAccount',
                config('constants.email_type.account_verify')
            );

            return $this->success(201, [
                'user' => $user
            ], 'Your account has been verified.');

        } catch (\Throwable $e) {
            $this->errorLog($request, $e, 'AuthController register action');
            return $this->error(400, "Register failed.");
        }

    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function cancelBooking(Request $request): JsonResponse
    {
        $booking_number = base64_decode($request->post('booking_number'));
        $user = $request->user();
        try {
            DB::beginTransaction();

            $order = Order::query()->where('booking_number', $booking_number)->first();
            $timezone = $order->business['timezone'];
            $utcTime = timezone_calculate(0, $timezone);

            if($user->role['name'] !== "business_owner"){
                if ($order->check_in >= Carbon::now()) {
                    $order['status'] = 'canceled';
                    $order->save();
                } else {
                    return response()->json(['message' => __("general.emails.bookingCanceledFailed"), 400]);
                }
            }else{
                $new_date = Carbon::now()->addHours($utcTime - 1)->toDateTimeString();

                if ($order->check_in >= $new_date) {
                    $order['status'] = 'canceled';
                    $order->save();
                } else {
                    return response()->json(['message' => __("general.emails.bookingCanceledFailed"), 400]);
                }
            }

            DB::commit();
            return $this->success(200, ['order' => $order], __("general.emails.bookingSuccessfullyCanceled"));
        } catch (\Throwable $e) {
            DB::rollback();
//            dd($e->getMessage());
            $this->errorLog($request, $e, 'Admin/BookingController cancel action', $user->id);
            return $this->error(400, "Booking cancel failed.");
        }

    }

    public function resendVerification(Request $request): JsonResponse
    {
        $request->user()->sendEmailVerification();

        return $this->success(200, [], 'Verification mail send successfully.');
    }

    public function verify(Request $request, $hash): JsonResponse
    {
        $user = $request->user();

//        try {
            DB::beginTransaction();

            $data = json_decode(base64_decode($hash));

            if (!hash_equals((string)$data->id, (string)$user->getKey())) {
                throw new Exception('This action is unauthorized.', 403);
            }

            if (!hash_equals((string)$data->hash, sha1($user->getEmailForVerification()))) {
                throw new Exception('This action is unauthorized.', 403);
            }

            if (!isset($data->expires) && !isset($data->signature)) {
                throw new Exception('This action is unauthorized.', 403);
            }

            $parameters = [
                'id' => $data->id,
                'hash' => $data->hash,
                'expires' => $data->expires
            ];

            ksort($parameters);

            $checkHash = hash_hmac('sha256',
                URL::Route('verification.verify', $parameters),
                config('app.key'));


            if (!hash_equals($checkHash, $data->signature)) {
                throw new Exception('This action is unauthorized.', 403);
            }

            if (!$user->hasVerifiedEmail()) {

                $user->update([
                    "email_verified_at" => date('Y-m-d h:i:s'),
                    "status" => config('constants.user_status.verified')
                ]);
            }

            DB::commit();
            return $this->success(200, [], 'Account verified successfully.');

//        } catch (Exception $e) {
//            DB::rollback();
//            return $this->error($e->getCode(), $e->getMessage());
//        } catch (\Throwable $e) {
//            DB::rollback();
//            $this->errorLog($request, $e, $user->id, 'AuthController verify action');
//            return $this->error(400, "Account verified  failed.");
//        }

    }

    public function verificationNotice(): JsonResponse
    {
        return $this->error(403, 'This action is unauthorized.');
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        try {

            $user = User::query()->where('email', $request->post('email'))->firstOrFail();
            /*** @var User $user */
            $data = $request->validated();
//            $data = $request->all();

            $this->resetHashPassword($user);

            return $this->success(200, [], 'Email has been sent.');

        } catch (Throwable $throwable) {
//            dd($throwable->getMessage());
            return $this->error($throwable->getMessage(), $throwable->getCode());
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function forgotPassword(Request $request){
        $data = $request->all();
        $user = User::query()->where('email', $data['email'])->firstOrFail();

        if($user){
            $password = Str::random(10);
            $user->password = Hash::make($password);
            $user->save();
//            return response()->json($user->password);


            $viewData = [
                'subject' => __('general.emails.ResetPasswordToUser.subject'),
                'email' => $user->email,
                'user' => $user,
                'newPassword' => $password
            ];

            EmailCreator::create(
                $user->id,
                $user->email,
                $viewData['subject'],
                view('emails.ResetPasswordToUser', $viewData)->render(),
                'emails.ResetPasswordToUser',
                config('constants.email_type.reset_password')
            );

        }
        return response()->json('success');
    }

    public function setNewPassword(SetNewPasswordRequest $request): JsonResponse
    {

        try {
            // Decrypt the data from the hash
            $data = Crypt::decrypt($request->post('hash'));

            $user = User::query()->find($data['user_id']);
            /*** @var User $user */

            // Check the link expiration
            $expires = Carbon::createFromTimestamp($data['expires']);
            if ($expires->lessThan(now())) {
                throw new Exception('The link is expired');
            }

            // Setting a new password
//            $this->setNewPassword($user, $request->post('password'));

            $user->password = Hash::make($request->post('password'));
            $user->save();

            return $this->success(200, [], 'Password has been successfully changed.');
        } catch (Throwable $throwable) {
            return $this->error($throwable->getMessage(), $throwable->getCode());
        }
    }

    public function resetHashPassword(User $user)
    {
        // Get the hash for user validation
        $hash = $this->getResetPasswordHash($user);
        // Send the link to the user email

        $viewData = [
            'subject' => __('general.emails.ResetPassword.subject'),
            'email' => $user->email,
            'name' => $user->name,
            'url' => $hash
        ];

        EmailCreator::create(
            $user->id,
            $user->email,
            $viewData['subject'],
            view('emails.ResetPassword', $viewData)->render(),
            'emails.ResetPassword',
            config('constants.email_type.reset_password')
        );

    }

    public function getResetPasswordHash($user){
        return Crypt::encrypt([
            'user_id' => $user->id,
            'expires' => Carbon::now()->addHours(config('auth.password_reset_expire'))->timestamp
        ]);
    }
}
