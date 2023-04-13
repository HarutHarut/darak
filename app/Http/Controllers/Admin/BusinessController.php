<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Admin\Business\ChangeStatus;
use App\Http\Requests\Admin\Business\Show;
use App\Http\Requests\Admin\Business\Update;
use App\Luglocker\Email\EmailCreator;
use App\Luglocker\Updaters\BusinessUpdater;
use App\Models\Business;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\QueryBuilder;
use \Throwable;

class BusinessController extends ApiController
{
    use BusinessUpdater;

    public function index(Request $request): JsonResponse
    {
        $admin = $request->user();
        $data = $request->all();
        try {
            $businesses = QueryBuilder::for(Business::class)
                ->with('user')
                ->withCount('branches','sizes')
                ->allowedFilters(['status', 'publish']);
            if(isset($data['search']) && $data['search'] !== null){
                $businesses = $businesses->where(function ($query) use ($data) {
                    $query->where('name', 'like', '%' . $data['search'] . '%')
                        ->orWhere('address', 'like', '%' . $data['search'] . '%')
                        ->orWhereHas('user', function ($q) use ($data) {
                            $q->where('name', 'like', '%' . $data['search'] . '%');
                        });
                });

            }

            $businesses = $businesses->orderByDesc('created_at')->paginate(config('constants.pagination.perPage'));
            return $this->success(200, [
                'businesses' => $businesses,
                'business_status' => config('constants.business_status'),
                'business_publish' => config('constants.business_publish')
            ]);

        } catch (\Throwable $e) {
            $this->errorLog($request, $e, $admin->id, 'Admin/BusinessController index action');
            return $this->error(400, $e->getMessage());
        }
    }

    public function show(Show $request, int $id): JsonResponse
    {
        $admin = $request->user();

        try {
            $business = Business::query()->with('branches')->find($id);
            return $this->success(200, $business);

        } catch (\Throwable $e) {
            $this->errorLog($request, $e, $admin->id, 'Admin/BusinessController show action');
            return $this->error(400, 'Something was wrong');
        }
    }

    public function update(Update $request): JsonResponse
    {
        $data = $request->validated();
        $admin = $request->user();
        try {

            $user = User::query()->find($data['user_id']);
            $business = $user->business;

            DB::beginTransaction();

            $business = $this->businessUpdate($business, $data);

            DB::commit();
            return $this->success(200, [
                'business' => $business
            ], __('businessUpdatedSuccessfully'));
        } catch (\Throwable $e) {
            DB::rollback();
            $this->errorLog($request, $e, $admin->id, 'Admin/BusinessController update action');
            return $this->error(400, $e->getMessage());
        }
    }

    public function changeStatus(ChangeStatus $request, int $id): JsonResponse
    {
        $data = $request->validated();
//        return response()->json($data);
        $admin = $request->user();
        $status = 0;
        $blocked = '';

        try {
            DB::beginTransaction();

            $business = Business::query()->find($id);

            if($business->status == 2){
                $blocked = 'unblocked';
            }

            $business->status = $data['status'];
//            $business = $this->businessUpdate($business, $data);
            $business->save();

            $businessOwner = $business->user;

            if ($business->status == config('constants.business_status.blocked')) {

                foreach ($business->branches as $item){
                    $item->working_status = 0;
                    $item->save();
                }

                $viewData = [
                    'subject' => __('general.emails.BusinessBlocked.subject'),
                    'email' => $businessOwner->email,
                    'user' => $businessOwner,
                    'business' => $business
                ];

                EmailCreator::create(
                    $businessOwner->id,
                    $businessOwner->email,
                    $viewData['subject'],
                    view('emails.BusinessBlocked', $viewData)->render(),
                    'emails.BusinessBlocked',
                    config('constants.email_type.business_block')
                );

            }

            if ($business->status == config('constants.business_status.verified') && $blocked !== 'unblocked') {
                foreach ($business->branches as $item){
                    $item->working_status = 1;
                    $item->save();
                }
                $status = 1;
                $viewData = [
                    'subject' =>  __('general.emails.BusinessVerified.subject'),
                    'email' => $businessOwner->email,
                    'user' => $businessOwner,
                    'business' => $business
                ];

                EmailCreator::create(
                    $businessOwner->id,
                    $businessOwner->email,
                    $viewData['subject'],
                    view('emails.BusinessVerified', $viewData)->render(),
                    'emails.BusinessVerified',
                    config('constants.email_type.business_verify')
                );

            }elseif($business->status == config('constants.business_status.verified') && $blocked == 'unblocked'){
                foreach ($business->branches as $item){
                    $item->working_status = 1;
                    $item->save();
                }
                $status = 1;
                $viewData = [
                    'subject' =>  __('general.emails.BusinessUnBlocked.subject'),
                    'email' => $businessOwner->email,
                    'user' => $businessOwner,
                    'business' => $business
                ];

                EmailCreator::create(
                    $businessOwner->id,
                    $businessOwner->email,
                    $viewData['subject'],
                    view('emails.BusinessUnBlocked', $viewData)->render(),
                    'emails.BusinessUnBlocked',
                    config('constants.email_type.business_verify')
                );
            }




            foreach ($business->branches as $branch){
                $branch->working_status = $status;
                $branch->save();
            }

            DB::commit();
            return $this->success(200, ['business' => $business], "Business status changed successfully");
        } catch (\Throwable $e) {
            DB::rollback();
            $this->errorLog($request, $e, 'Admin/BusinessController changeStatus action', $admin->id);
            return $this->error(400, $e->getMessage());
        }
    }
}
