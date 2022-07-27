<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Admin\Branch\ChangeStatus;
use App\Http\Requests\Admin\Branch\Update;
use App\Http\Requests\Admin\Branch\Show;
use App\Jobs\BranchBlockedJob;
use App\Luglocker\Email\EmailCreator;
use App\Luglocker\Updaters\BranchUpdater;
use App\Models\Branch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use \Throwable;

class BranchController extends ApiController
{
    use BranchUpdater;

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $data = $request->all();

        try {
            $branches = Branch::query()
                ->with(['city', 'currency', 'business'])
                ->withCount(['lockers', 'feedbacks']);

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

    public function show(Show $request, $id): JsonResponse
    {
        $admin = $request->user();

        try {
            $branch = Branch::query()
                ->with([
                    'feedbacks',
                    'lockers',
                    'closingTimes',
                    'openingTimes',
                    'bookings',
                    'socialNetworkUrls'
                ])->find($id);

            return $this->success(200, $branch);

        } catch (\Throwable $e) {
            $this->errorLog($request, $e, $admin->id, 'Admin/BranchController show action');
            return $this->error(400, $e->getMessage());
        }
    }

    public function update(Update $request, $id): JsonResponse
    {
        $data = $request->validated();
        $admin = $request->user();

        try {
            $branch = Branch::query()->find($id);

            DB::beginTransaction();

            /**
             * @var $branch Branch
             */
            $branch = $this->branchUpdate($branch, $data);

            DB::commit();
            return $this->success(200, ['branch' => $branch], "Branch updated successfully.");
        } catch (\Throwable $e) {
            DB::rollback();
            $this->errorLog($request, $e, 'Admin/BranchController update action', $admin->id);
            return $this->error(400, $e->getMessage());
        }
    }

    public function changeStatus(ChangeStatus $request, $id): JsonResponse
    {
        $data = $request->validated();
        $admin = $request->user();

        try {
            DB::beginTransaction();

            /**
             * @var $branch Branch
             */
            $branch = Branch::query()->find($id);

            $branch = $this->branchUpdate($branch, $data);

            if ($branch->status == config('constants.branch_status.blocked')) {
                $branchOwner = $branch->business->user;

                $viewData = [
                    'subject' => __('general.emails.BranchBlocked.subject'),
                    'email' => $branchOwner->email
                ];
                EmailCreator::create(
                    $branchOwner->id,
                    $branchOwner->email,
                    $viewData['subject'],
                    view('emails.BranchBlocked', $viewData)->render(),
                    'emails.BranchBlocked',
                    config('constants.email_type.branch_block')
                );

            }

            DB::commit();
            return $this->success(200, ['branch' => $branch], "Branch status changed successfully");
        } catch (\Throwable $e) {
            DB::rollback();
            $this->errorLog($request, $e, 'Admin/BranchController changeStatus action', $admin->id);
            return $this->error(400, $e->getMessage());
        }
    }

    public function destroy(Request $request, $id): JsonResponse
    {
        $admin = $request->user();
        try {
            DB::beginTransaction();

            $branch = Branch::destroy($id);

            DB::commit();

            return $this->success(200, ['branch' => $branch], "Branch deleted successfully");
        } catch (\Throwable $e) {
            DB::rollback();
            $this->errorLog($e, 'Admin/BranchController destroy action', $admin->id);
            return $this->error(400, $e->getMessage());
        }
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function recommendedStatus($id)
    {
        $branch = Branch::find($id);
        $branch->recommended = !$branch->recommended;
        $branch->save();

        return response()->json($branch);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function workingStatus($id)
    {
        $branch = Branch::find($id);

        if ($branch->working_status == 0 || $branch->working_status == 2){
            $branch->working_status = 1;

            $viewData = [
                'subject' => __('general.emails.BranchVerified.subject'),
                'email' => $branch->email,
                'branch' => $branch,
            ];

            EmailCreator::create(
                $branch->business->user->id,
                $branch->email,
                $viewData['subject'],
                view('emails.BranchVerified', $viewData)->render(),
                'emails.BranchVerified',
                config('constants.email_type.book_cancel_by_user')
            );

        }else{
            $branch->working_status = 0;

            $viewData = [
                'subject' => __('general.emails.BranchBlocked.subject'),
                'email' => $branch->email,
                'branch' => $branch,
            ];
            EmailCreator::create(
                $branch->business->user->id,
                $branch->email,
                $viewData['subject'],
                view('emails.BranchBlocked', $viewData)->render(),
                'emails.BranchBlocked',
                config('constants.email_type.book_cancel_by_user')
            );
        }

        $branch->save();

        return response()->json($branch);
    }
}
