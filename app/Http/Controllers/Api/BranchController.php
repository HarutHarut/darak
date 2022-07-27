<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\Branch\Create;
use App\Http\Requests\Api\Branch\MediaUpdate;
use App\Luglocker\Email\EmailCreator;
use App\Luglocker\General;
use App\Luglocker\Price\BookPriceCalculator;
use App\Luglocker\Price\BranchCalculate;
use App\Models\Booking;
use App\Models\Business;
use App\Models\Currency;
use App\Models\Feedback;
use App\Models\Locker;
use App\Models\OpeningTime;
use App\Models\Order;
use App\Models\Size;
use App\Models\SocialNetworkUrl;
use App\Models\SpecialClosingTime;
use App\Repositories\BranchRepository;
use App\Services\BranchService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\City;
use App\Models\Branch;
use Mockery\Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Luglocker\Media\MediaActions;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\Branch\Update;
use App\Luglocker\Updaters\BranchUpdater;
use App\Services\ImageService;

class BranchController extends ApiController
{
    use MediaActions;
    use BranchUpdater;
    use BookPriceCalculator;

    protected ImageService $imageService;
    protected BranchService $branchService;
    protected BranchRepository $branchRepository;

    public function __construct(
        ImageService $imageService,
        BranchService $branchService,
        BranchRepository $branchRepository
    )
    {
        $this->imageService = $imageService;
        $this->branchService = $branchService;
        $this->branchRepository = $branchRepository;
    }

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $data = $request->all();
        $user_business = Business::query()->where('user_id', $user->id)->exists();

        $data['business_id'] = [];
        if ($user->isBusiness() && $user_business) {
            $data['business_id'] = $user->business->id;
        }
        try {
            $branches = Branch::query();
            if (!$user->isAdmin() || $request->has('business_id')) {
                $branches = $branches->where('business_id', $data['business_id']);
            }

            $branches = $branches->with(['city', 'currency', 'business'])
                ->withCount(['lockers', 'feedbacks']);
//                ->where('status', '=', config('constants.branch_status.verified'))
//                ->where('working_status', '=', config('constants.branch_open_status.open'));

            if (isset($data['search']) && $data['search'] != null) {
                $branches = $branches->where('name', 'like', '%' . $data['search'] . '%')
                    ->orWhere('address', 'like', '%' . $data['search'] . '%')
                    ->orWhere('slug', 'like', '%' . $data['search'] . '%')
                    ->orWhereHas('city', function ($q) use ($data) {
                        $q->where('name', 'like', '%' . $data['search'] . '%');
                    });
            }

            $branches = $branches->paginate(config('constants.pagination.perPage'));

            return $this->success(200, ['branches' => $branches]);

        } catch (\Throwable $e) {
            DB::rollback();
            $this->errorLog($request, $e, 'BranchController index action', $user->id);
            return $this->error(400, "Branch index failed.");
        }
    }

    public function searchBranches(Request $request): JsonResponse
    {
        $data = $request->all();
        $user = $request->user();
        if ($user->isBusiness()) {
            $branches = $this->branchRepository->businessBranchSearch($data, $user['id']);
        } else {
            $branches = $this->branchRepository->adminBranchSearch($data);
        }

        return $this->success(200, ['branches' => $branches]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function searchLockers(Request $request)
    {
        $data = $request->all();
        $user = $request->user();
        $user_currency = General::resolveCurrency($request);

        try {

            DB::beginTransaction();
            $total = 0;
            $data['check_in'] = $data['check_in'] . ' ' . $data['start_time']; //1651226400
            $data['check_out'] = $data['check_out'] . ' ' . $data['end_time']; //1651227300  1651230000
            $startWeekDay = date("w", strtotime($data['check_in']));
            $endWeekDay = date("w", strtotime($data['check_out']));


            foreach ($data['data'] as $item) {
                $lockers = Locker::with('prices')
                    ->where('branch_id', $data['branch_id'])
                    ->where('size_id', $item['size_id']);
                $lockers_array = $lockers->get();
                foreach ($lockers_array as $locker) {
                    $booking = Booking::query()
                        ->where('branch_id', $data['branch_id'])
                        ->where('locker_id', $locker->id)
                        ->where(function ($query) use ($data) {
                            $query
                                ->where(function ($q) use ($data) {
                                    $q->where('start', '<=', $data['check_in'])
                                        ->where('end', '>=', $data['check_in']);
                                })
                                ->orWhere(function ($q) use ($data) {
                                    $q->where('start', '<=', $data['check_out'])
                                        ->where('end', '>=', $data['check_out']);
                                })
                                ->orWhere(function ($q) use ($data) {
                                    $q->where('start', '>=', $data['check_in'])
                                        ->where('end', '<=', $data['check_out']);
                                });
                        })->count();

                    $lockerSpecialClosingTimes = SpecialClosingTime::query()
                        ->where(['locker_id' => $locker->id])
                        ->where(function ($query) use ($data) {
                            $query->where(function ($q) use ($data) {
                                $q->where('currentDay', explode(' ', $data['check_in'])[0])
                                    ->where('start', explode(' ', $data['check_in'])[1]);
                            })->orWhere(function ($q) use ($data) {
                                $q->where('currentDay', explode(' ', $data['check_out'])[0])
                                    ->where('start', explode(' ', $data['check_out'])[1]);
                            });
                        })
                        ->count();
                    if ($lockerSpecialClosingTimes) {
                        return $this->error(400, __('general.lockerQuantityDoesNoteMatch'));
                    }

                    $lockerOpeningTimes = OpeningTime::query()
                        ->where('branch_id', $data['branch_id'])
                        ->where('status', 1)
                        ->where(function ($q) use ($data, $startWeekDay) {
                            $q->where('weekday', $startWeekDay)
                                ->where('start', '<=', explode(' ', $data['check_in'])[1])
                                ->where('end', '>=', explode(' ', $data['check_in'])[1]);
                        })
                        ->get();

                    if (count($lockerOpeningTimes) !== 0) {
                        $lockerOpeningEndTimes = OpeningTime::query()
                            ->where('branch_id', $data['branch_id'])
                            ->where('status', 1)
                            ->where(function ($q) use ($data, $endWeekDay) {
                                $q->where('weekday', $endWeekDay)
                                    ->where('start', '<=', explode(' ', $data['check_out'])[1])
                                    ->where('end', '>=', explode(' ', $data['check_out'])[1]);
                            })
                            ->get();

                        if (count($lockerOpeningEndTimes) == 0){
                            return $this->error(400, __('general.openingTimeErrorMessage'));
                        }

                    }else{
                        return $this->error(400, __('general.openingTimeErrorMessage'));
                    }

                    if ($locker['count'] - $booking >= $item['count']) {
                        $locker_arr = $this->calculatePrice($locker, $data['check_in'], $data['check_out']);
                        $total += $locker_arr['total'] * $item['count'];
                    } else {
                        return $this->error(400, __('general.lockerQuantityDoesNoteMatch'));
                    }
                }
            }

            $branch = Branch::find($data['branch_id']);
            $business_currency = $branch->business['currency'] ?? 'EUR';
//            $branch_currency = $branch->currency['name'] ?? 'EUR';
//            $user_currency = $user->currency ?? ($data['currency'] ?? 'EUR');

            $total_price = $this->currencyChangeFromUser($total, $business_currency, $user_currency, false) . ' ' . $user_currency;
            $booking_number = (int)($data['branch_id'] . mt_rand(10, 99) . Carbon::now()->getTimestamp());
            if (isset($data['booking']) && $user) {
                $count = 0;
                $sizeArr = [];
                foreach ($data['data'] as $item) {
                    $sizes = Size::find($item['size_id']);
                    $sizes['count'] = $item['count'];
                    $sizeArr[] = $sizes;
                    $count++;
                    $lockers = Locker::with('prices')->where('branch_id', $data['branch_id'])->where('size_id', $item['size_id'])->get();
                    foreach ($lockers as $locker) {
                        for ($i = 0; $i < $item['count']; $i++) {
                            $this->createBooking($booking_number, $user->id, $data['branch_id'], $locker->id, $data['check_in'], $data['check_out']);
                        }
                    }
                }

                $order = Order::create([
                    'business_id' => $branch->business['id'],
                    'user_id' => $user->id,
                    'booking_number' => $booking_number,
                    'check_in' => $data['check_in'],
                    'check_out' => $data['check_out'],
                    'price' => $total,
                    'currency' => $business_currency,
                    'status' => 'pending',
                    'pay_type' => 'cache'
                ]);

//                return response()->json(gettype(json_encode($sizeArr)));

                $view = 'emails.BookedUser';
                $viewData = [
                    'subject' => __("general.emails.BookedUser.subject"),
                    'user' => $user,
                    'email' => $user->email,
                    'order' => $order,
                    'branch' => $branch,
                    'bookingCount' => $count,
                    'sizeArr' => $sizeArr,
                ];

                EmailCreator::create(
                    $user->id,
                    $user->email,
                    $viewData['subject'],
                    view($view, $viewData)->render(),
                    $view,
                    config('constants.email_type.book_user')
                );

                $view = 'emails.BookedBusinessOwner';
                $viewData = [
                    'subject' => __("general.emails.BookedBusinessOwner.subject"),
                    'user' => $user,
                    'email' => $branch->email,
                    'order' => $order,
                    'branch' => $branch,
                    'bookingCount' => $count,
                    'sizeArr' => $sizeArr,
                ];
                EmailCreator::create(
                    $branch->business->user_id,
                    $branch->email,
                    $viewData['subject'],
                    view($view, $viewData)->render(),
                    $view,
                    config('constants.email_type.book_business_owner')
                );
//                BookedBusinessOwnerJob::dispatch($branch, $order);

            }
            DB::commit();
            return $this->success(200, ['total_price' => $total_price]);

        } catch (\Throwable $e) {
            DB::rollback();
            $this->errorLog($request, $e, 'BranchController searchLockers action');
            return $this->error(400, "Lockers failed.");
        }
    }

    public function createBooking($booking_number, $user_id, $branch_id, $locker_id, $check_in, $check_out, $amount = 1)
    {

        Booking::create([
            'booking_number' => $booking_number,
            'booker_id' => $user_id,
            'branch_id' => $branch_id,
            'locker_id' => $locker_id,
            'start' => $check_in,
            'end' => $check_out,
            'amount' => $amount
        ]);
    }

    public function all(Request $request): JsonResponse
    {
        $data = $request->input("business_id");
        $user = $request->user();
        $business = $user->business;
        if (isset($business["id"])) {
            $businessId = $business->id;
        } else {
            $businessId = $data;
        }

        try {

            $branches = Branch::query()
                ->select('id', 'name')
//                ->where('business_id', '=', $businessId)
                ->where('status', '=', config('constants.branch_status.verified'))
                ->where('working_status', '=', config('constants.branch_open_status.open'));

            if (!$user->isAdmin()) {
                $branches = $branches->where('business_id', '=', $businessId);
            }


            $branches = $branches->get();

            return $this->success(200, ['branches' => $branches]);

        } catch (\Throwable $e) {
            DB::rollback();
            $this->errorLog($request, $e, 'BranchController all action', $user->id);
            return $this->error(400, "Branch all failed.");
        }
    }

    public function map(Request $request): JsonResponse
    {
        $date = Carbon::now();

        try {

            $branches = $this->branchRepository->mapBranchFilter($request->all());
            foreach ($branches as $branch) {
                $weekCount = count($branch->openingTimes->where('weekday', $date->dayOfWeekIso)->where('status', 1));

                if ($weekCount !== 0) {
                    $branch['open_today'] = 1;
                } else {
                    $branch['open_today'] = 0;
                }
                $openDayCount = 0;
                foreach ($branch->openingTimes as $item) {
                    if ($item->start == '00:00:00' && $item->end == '23:59:00' && $item->status == 1) {
                        $openDayCount++;
                    }
                }
                if ($openDayCount == 7) {
                    $ids[] = $branch->id;
                    $branch['open_today'] = 1;
                    $branch['open_day_night'] = 1;
                } else {
                    $branch['open_day_night'] = 0;
                }
                $branch['min_price'] = BranchCalculate::minPrice($branch->lockers);

            }

            return $this->success(200, ['branches' => $branches]);

        } catch (\Throwable $e) {
            DB::rollback();
            $this->errorLog($request, $e, 'BranchController map action');
            return $this->error(400, "Branch all failed.");
        }
    }

    public function recommended(Request $request): JsonResponse
    {

        try {

            $branches = Branch::query()
                ->where('status', config('constants.branch_status.verified'))
                ->where('recommended', 1)
                ->where('working_status', config('constants.branch_open_status.open'))
                ->with('city')
                ->limit(6)
                ->get();
//return response()->json(123123);
            return $this->success(200, ['branches' => $branches]);

        } catch (\Throwable $e) {
            DB::rollback();
            $this->errorLog($request, $e, 'BranchController all action');
            return $this->error(400, "Branch all failed.");
        }
    }

    public function show(Request $request, $slug): JsonResponse
    {
        $user = $request->user();

        try {

            $branch = Branch::query()
                ->with([
                    'feedbacks.user',
                    'lockers.prices',
                    'lockers.size',
                    'openingTimes',
                    'business',
                    'city',
                    'media',
                    'socialNetworkUrls'
                ])
                ->where('slug', $slug)
                ->first();
            $branch['currency'] = $user->currency ? Currency::where('name', $user->currency)->first()->name : 'EUR';

            return $this->success(200, ['branch' => $branch]);

        } catch (\Throwable $e) {
            DB::rollback();
            $this->errorLog($request, $e, 'BranchController show action', $user->id);
            return $this->error(400, "Branch show failed.");
        }
    }

    /**
     * @param Request $request
     * @param $slug
     * @return JsonResponse
     */
    public function singleBranch(Request $request, $slug): JsonResponse
    {
        $user_currency = General::resolveCurrency($request);

        try {
            $branch = Branch::with(['media', 'currency', 'socialNetworkUrls', 'business', 'openingTimes'])->where('slug', $slug)->first();
            $business_currency = $branch->business['currency'] ?? 'EUR';

            $lockers = Locker::query()
                ->with(['prices', 'size'])
                ->where('branch_id', $branch->id)->get();
            $branch['feedbacks'] = Feedback::query()
                ->with('user')
                ->where('branch_id', $branch->id)
                ->paginate(config('constants.pagination.perPage'));
            $branch['average_rating'] = BranchCalculate::averageRating($branch->feedbacks);
            $branch['average_rating_double'] = round($branch['average_rating']);
            $branchMinPrice = BranchCalculate::minPrice($lockers);

            $branch_currency = $branch->currency['name'] ?? 'EUR';
            $branch['min_price'] = $this->currencyChangeFromUser($branchMinPrice, $business_currency, $user_currency, false) . ' ' . $user_currency;
            foreach ($lockers as $locker){
                if($locker->price_per_day && $locker->price_per_day !== null){
                    $locker->price_per_day = $this->currencyChangeFromUser($locker->price_per_day, $business_currency, $user_currency, false) . ' ' . $user_currency;

                }
                if($locker->price_per_hour && $locker->price_per_hour !== null){
                    $locker->price_per_hour = $this->currencyChangeFromUser($locker->price_per_hour, $business_currency, $user_currency, false) . ' ' . $user_currency;

                }
                foreach ($locker->prices as $item){
                    if($item->price && $item->price !== null){
                        $item->price = $this->currencyChangeFromUser($item->price, $business_currency, $user_currency, false) . ' ' . $user_currency;

                    }
                }
            }

            $recommendedBranches = BranchCalculate::recommendedBranch($branch);


            if (isset($recommendedBranches)) {
                foreach ($recommendedBranches as $recommendedBranch) {
//                $recommendedBranch['recommended_average_rating'] = BranchCalculate::averageRating($recommendedBranch['feedbacks']);
                    if (isset($recommendedBranch->lockers)) {
                        $branchMinPrice = BranchCalculate::minPrice($recommendedBranch->lockers);

                        $recommendedBranch['min_price'] = $this->currencyChangeFromUser($branchMinPrice, $business_currency, $user_currency, false) . ' ' . $user_currency;
                    } else {
                        $recommendedBranch['min_price'] = '';
                    }

                    $recommendedBranch['average_rating'] = BranchCalculate::averageRating($recommendedBranch->feedbacks);
                    $recommendedBranch['average_rating_double'] = round($recommendedBranch['average_rating']);

                    unset($recommendedBranch['feedbacks']);
                    unset($recommendedBranch['description']);
                }
            }


            return $this->success(200, [
                'branch' => $branch,
                'lockers' => $lockers,
                'recommended' => $recommendedBranches
            ]);
        } catch (\Throwable $e) {
            DB::rollback();
            $this->errorLog($request, $e, 'BranchController show action', $user->id ?? null);
            return $this->error(400, "Branch show failed.");
        }
    }


    public function create(Create $request): JsonResponse
    {
        $data = $request->all();
        $user = $request->user();
        $business_id = $user->business->id ?? $request->get('business_id') ?? null;
        $country_code = '';

        if (!$business_id) {
            throw new Exception('Business not found.', 404);
        }

        try {
            DB::beginTransaction();

            $city = City::query()
                ->where('name', '=', $data['city']['name'])
                ->first();

            if (!$city) {
                $city = City::query()
                    ->create($data['city']);
            }

            $meta = array('en' => null, 'ru' => null, 'ch' => null, 'am' => null, 'fr' => null);
            $business = Business::find($business_id);
            $currency = Currency::where('name', $business->currency)->first();
            if (isset($data['phone_country']) && isset($data['phone_code'])){
                $country_code = json_encode(array('country' => $data['phone_country'], 'code' => $data['phone_code']));
            }
            if(empty($data['phone'])){
                $country_code = '';
            }

            $branch = Branch::query()->create([
                'business_id' => $business_id,
                'currency_id' => $currency->id ?? 3,
                'city_id' => $city->id,
                'name' => $data['name'],
                'slug' => $this->branchService->getSetSlug($data['name']['en']),
                'lat' => $data['lat'],
                'lng' => $data['lng'],
                'phone' => $data['phone'],
                'country_code' => $country_code,
                'email' => $data['email'] ?? '',
                'address' => $data['address'],
                'status' => $data['status'],
                'working_status' => 2,
                'card_payment' => $data['card_payment'],
                'is_bookable' => 1,
                'description' => $data['description'],
                'meta_title' => $data['meta_title'] ?? $meta,
                'meta_description' => $data['meta_description'] ?? $meta,
                'meta_keywords' => $data['meta_keywords'] ?? $meta,
            ]);
            if(isset($data['is_bookable']) && ['is_bookable'] !== null){
                $branch->is_bookable = $data['is_bookable'];
            }

            $branch->branch_number = $branch->id + 10000;
            $branch->save();

            if ($request->file('logo')) {
                $uniqFilename = \Illuminate\Support\Str::random(10) . "_" . Carbon::now()->timestamp;
                $fileName = $this->imageService->compressImage($data['logo'], storage_path('app/public/branches/logo/') . $uniqFilename, config('constants.compressImage'));
                $branch['logo'] = config("app.beck_url") . '/storage/branches/logo/' . $fileName;
                $branch->save();
            }
            foreach ($data['socialMedia'] as $media => $key) {
                if ($key && $key !== 'null') {
                    SocialNetworkUrl::query()->create([
                        'business_id' => $business_id,
                        'branch_id' => $branch->id,
                        'type' => $media,
                        'url' => $key,
                        'created_at' => Carbon::now()
                    ]);
                }
            }
            for ($i = 1; $i <= 7; $i++) {
                OpeningTime::query()->create([
                    'weekday' => $i,
                    'start' => '00:00',
                    'end' => '23:59',
                    'branch_id' => $branch->id
                ]);
            }
            if (isset($data['media'])) {
                foreach ($data['media'] as $key => $file) {
                    $mediaKey = false;

                    if ($data['mediaKey'][$key] == 1) {
                        $mediaKey = true;
                    }
                    $this->addSingleMedia($branch, $file, false, 'branches', $mediaKey);
                }
            }

            DB::commit();
            return $this->success(200, ['branch' => $branch], __('adminMastBeVerified'));
        } catch (\Throwable $e) {
            DB::rollback();
            $this->errorLog($request, $e, 'BranchController create action', $user->id);
            return $this->error(400, "Branch create failed.");
        }
    }

    public function update(Update $request): JsonResponse
    {
        $data = $request->validated();
        $user = $request->user();
        $business_id = $user->business->id ?? $request->get('business_id') ?? null;

        if (!$business_id) {
            throw new Exception('Business not found.', 404);
        }

        try {
            DB::beginTransaction();

            $city = City::query()
                ->where('name', '=', $data['city']['name'])
                ->first();

            if (!$city) {
                $city = City::query()
                    ->create($data['city']);
            }

            $data['city_id'] = $city->id;
            $data['business_id'] = $business_id;

            $branch = Branch::query()
                ->where('id', $data['id'])
                ->first();
            if ($branch == null) {
                throw new Exception('Branch not found.', 404);
            }
            $branch->branch_number = $branch->id + 10000;
            $branch->is_bookable = $data['is_bookable'];

            $branch->country_code = json_decode($branch['country_code']);
            $branch->country_code->country = $data['phone_country'];
            $branch->country_code->code = $data['phone_code'];
            $branch->country_code = json_encode($branch->country_code);

            $branch->save();

            foreach ($data['socialMedia'] as $media => $key) {
                if ($media) {
                    $social = SocialNetworkUrl::query()->where('branch_id', $data['id'])->where('type', $media);
                    if ($social->count()) {
                        if ($key) {
                            $social->update(['url' => $key]);
                        } else {
                            $social->delete();
                        }
                    } else {
                        if ($key) {
                            SocialNetworkUrl::query()->create([
                                'business_id' => $data['business_id'],
                                'branch_id' => $data['id'],
                                'type' => $media,
                                'url' => $key,
                                'created_at' => Carbon::now()
                            ]);
                        }
                    }
                }
            }

            /**
             * @var $branch Branch
             */

            $this->branchUpdate($branch, $data);

            DB::commit();
            return $this->success(200, [], "Branch updated successfully.");
        } catch (\Throwable $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage(), 'line' => $e->getLine()]);
            $this->errorLog($request, $e, 'BranchController update action', $user->id);
            return $this->error(400, "Branch update failed.");
        }
    }

    public function updateMedia(MediaUpdate $request): JsonResponse
    {
        $data = $request->validated();
        $user = $request->user();

        try {
            DB::beginTransaction();

            $branch = Branch::query()
                ->where('id', '=', $data['id'])
                ->first();

            if ($branch == null) {
                throw new Exception('Branch not found.', 404);
            }

            /**
             * @var $branch Branch
             */

            $this->branchMediaUpdate($branch, $data);

            DB::commit();
            return $this->success(200, [], "Branch media updated successfully.");
        } catch (\Throwable $e) {
            DB::rollback();
            $this->errorLog($request, $e, 'BranchController update action', $user->id);
            return $this->error(400, "Branch updated failed.");
        }
    }

    public function branchesByBusinessId(Request $request)
    {
        $data = $request->all();
        $user = Auth::user();
        $business = Business::query()->where('user_id', $user->id)->first();
        $branchId = $this->branchService->getBranchId($data, $business->id);
        $openingTime = $this->branchService->getOpenHour($branchId, $data);
        $openingTime['branch'] = Branch::find($branchId);
        return response()->json($openingTime);
    }

    public function getBranchMinPrice(Request $request, $slug)
    {
        $user = $request->user();

        try {
            $branch = Branch::with('lockers')->where('slug', $slug)->first();
            $price = BranchCalculate::minPrice($branch->lockers);

            return $this->success(200, ['price' => $price]);
        } catch (\Throwable $e) {
            DB::rollback();
            $this->errorLog($request, $e, 'BranchController getBranchMinPrice action', $user->id);
            return $this->error(400, "Branch getBranchMinPrice failed.");
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function changeStatus(Request $request)
    {
        $data = $request->all();
        $branch = Branch::find($data['branch_id']);
        if ($data['status'] == 1) {
            $branch->status = 0;
        } else {
            $branch->status = 1;
        }
        $branch->save();

        return response()->json($branch);
    }

}
