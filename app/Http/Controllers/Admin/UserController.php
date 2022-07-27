<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Admin\User\Create;
use App\Http\Requests\Admin\User\Update;
use App\Http\Requests\CurrencyRequest;
use App\Luglocker\Email\EmailCreator;
use App\Models\Business;
use App\Models\Email;
use App\Models\User;
use App\Repositories\UserRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use \Throwable;

class UserController extends ApiController
{
    protected UserRepository $userRepository;


    public function __construct(
       UserRepository $userRepository
    ) {
        $this->userRepository = $userRepository;
    }


    public function index(Request $request): JsonResponse
    {
        $admin = $request->user();
        try {

            $users = User::query()
                ->with(['role', 'business'])
                ->withCount('bookings')
                ->whereHas('role', function ($query) {
                    $query->where('name', '<>', 'admin');
                })->paginate(config('constants.pagination.perPage'));

            return $this->success(200, ['users' => $users]);
        } catch (\Throwable $e) {
            $this->errorLog($request, $e, $admin->id, 'Admin/UserController index action');
            return $this->error(400, 'Something was wrong');
        }
    }


    public function update(Update $request): JsonResponse
    {
        $data = $request->validated();
//        return response()->json($data);
        $admin = $request->user();

        try {
            DB::beginTransaction();

            $user = User::query()->find($data['id']);

            $user->update($data);

            DB::commit();
            return $this->success(200, ['user' => $user->fresh()], "User updated successfully.");
        } catch (Exception $e) {
            DB::rollback();
            $this->errorLog($request, $e, 'Admin/UserController update action', $admin->id);
            return $this->error(400, $e->getMessage());
        }
    }

    /**
     * @param Create $request
     * @return JsonResponse
     */
    public function store(Create $request){
        $data = $request->validated();
        try {
            DB::beginTransaction();
            $user = User::query()->create([
                'name' => $data['name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'email_verified_at' => now(),
                'currency' => "EUR",
                'role_id' => isset($data['business_name']) ? 3 : 2,
                'password' => Hash::make($data['password']),
            ]);
            if (isset($data['business_name'])) {
                $name = new \stdClass();
                $name->ru = null;
                $name->sp = null;
                $name->ch = null;
                $name->am = null;
                $name->de = null;
                $name->fr = null;
                $name->en = $data['business_name'];
                $data['business_name'] = $name;
                Business::query()->create([
                    'user_id' => $user->id,
                    'name' => $data['business_name'],
                    'phone' => $data['phone'],
//                    'timezone' => $data['timezone'],
                    'currency' => $data['currency'] ?? 'EUR',
                ]);
            }

            DB::commit();
            return $this->success(201, [
                'user' => $user
            ], __('general.adminCreateUser'));

        } catch (\Throwable $e) {
            DB::rollback();
            $this->errorLog($request, $e, 'Admin/UserController create action');
            return $this->error(400, "Register failed.");
        }

//        return response()->json(3241234213423);
    }

    /**
     * @param CurrencyRequest $request
     * @return JsonResponse
     */
    public function userCurrency(CurrencyRequest $request)
    {
        try {
            $user = Auth::user();
            $data = $request->validated();
            $user->update($data);

            return $this->success(200, ['user' => $user],"User currency updated successfully.");
        } catch (Exception $e) {
            DB::rollback();
//            dd($e->getMessage());
            $this->errorLog($request, $e, 'The given data was invalid');
            return $this->error(400, $e->getMessage());
        }

    }

    public function destroy(Request $request, $id){
        $admin = $request->user();
        try {
            DB::beginTransaction();

            Business::where('user_id', $id)->delete();
            Email::where('user_to_id', $id)->delete();
            $user = User::destroy($id);

            DB::commit();
            return $this->success(200, ['user' => $user], "User deleted successfully");
        } catch (\Throwable $e) {
//            $this->errorLog($e, 'Admin/UserController destroy action', $admin->id);
            return $this->error(400, $e->getMessage());
        }

    }

    public function searchUser(Request $request): JsonResponse
    {
        $data = $request->all();

        return $this->success(200, ['users' => $this->userRepository->searchUser($data)]);
    }

    public function changeStatus(Request $request)
    {
        $data = $request->all();
        $user = User::find($data['user_id']);
        if($data['status'] == 1){
            $user->status = 0;
        }else{
            $user->status = 1;
        }
        $user->save();

        if($user->status == 0){
            $viewData = [
                'subject' => __('general.emails.UserBlocked.subject'),
                'email' => $user->email,
                'user' => $user,
            ];

            EmailCreator::create(
                $user->id,
                $user->email,
                $viewData['subject'],
                view('emails.UserBlocked', $viewData)->render(),
                'emails.UserBlocked',
                config('constants.email_type.book_cancel_by_user')
            );
        }

        if($user->status !== 0){
            $viewData = [
                'subject' => __('general.emails.UserActivity.subject'),
                'email' => $user->email,
                'user' => $user,
            ];

            EmailCreator::create(
                $user->id,
                $user->email,
                $viewData['subject'],
                view('emails.UserActivity', $viewData)->render(),
                'emails.UserActivity',
                config('constants.email_type.book_cancel_by_user')
            );
        }

        return response()->json($user);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function getUser($id){
        $user = User::with(['business.city', 'role'])->where('id', $id)->first();
        return response()->json($user);
    }

}
