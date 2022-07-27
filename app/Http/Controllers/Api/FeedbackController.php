<?php

namespace App\Http\Controllers\Api;

use App\Luglocker\Price\BranchCalculate;
use App\Models\Branch;
use App\Models\Order;
use \Throwable;
use Mockery\Exception;
use App\Models\Booking;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ApiController;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Requests\Api\Feedback\Create;
use App\Http\Requests\Api\Feedback\Delete;

class FeedbackController extends ApiController
{
    public function create(Create $request): JsonResponse
    {
        $data = $request->validated();
        $user = $request->user();


        try {

            $check = Order::query()
                ->where('user_id', $user->id)
                ->where('id', $data['order_id'])
                ->first();

            if ($check == null) {
                throw new Exception('The user is not authorized to create feedback on this booking.', 403);
            }

            DB::beginTransaction();

            $feedback = Feedback::query()
                ->create([
                    'user_id' => $user->id,
                    'branch_id' => $check->bookings->first()->branch_id,
                    'order_id' => $data['order_id'],
                    'text' => $data['feedback'],
                    'rating' => $data['rating']
                ]);

            $branch = Branch::find($check->bookings->first()->branch_id);
            $averageRatingDouble = BranchCalculate::averageRatingDouble($branch->feedbacks);
            $branch->avg_rating = $averageRatingDouble;
            $branch->save();

            DB::commit();

            return $this->success(200, ['feedback' => $feedback->fresh()], 'Feedback created successfully.');

        } catch (Exception $e) {
            DB::rollback();
            return $this->error($e->getCode(), $e->getMessage());
        } catch (\Throwable $e) {
            DB::rollback();
            $this->errorLog($request, $e, $user->id, 'FeedbackController create action');
            return $this->error(400, "Feedback create failed.");
        }
    }

    public function all(Request $request): JsonResponse
    {
        $data = $request->all();

        try {

            $user = $request->user();

            $reviews = Feedback::query()
                ->with(['branch:id,name','user:id,name'])
                ->whereHas('branch', function ($query) use ($user, $data) {
                    $query->where('business_id', '=', $user->business->id);
                    if(isset($data['branchId']) && $data['branchId'] !== null){
                        $query->where('id', $data['branchId']);
                     }
                })->paginate(config('constants.pagination.perPage'));

            $branches = Branch::where('business_id', $user->business->id)->get();

            return $this->success(200, ['reviews' => $reviews, 'branches' => $branches], "");
        } catch (\Throwable $e) {
            DB::rollback();
            $this->errorLog($request, $e, $user->id, 'FeedbackController all action');
            return $this->error(400, "Feedback get failed.");
        }
    }

    public function delete(Delete $request): JsonResponse
    {
        try {

            $data = $request->validated();
            $user = $request->user();

            Feedback::query()
                ->where('id', '=', $data['id'])
                ->delete();

            return $this->success(200, [], "Feedback deleted successfully.");
        } catch (\Throwable $e) {
            DB::rollback();
            $this->errorLog($request, $e, $user->id, 'FeedbackController delete action');
            return $this->error(400, "Feedback delete failed.");
        }
    }

    public function feedbacksShow(Request $request, $id){
        $user = $request->user();

        try {
            $feedbacks = Feedback::where('branch_id', $id)->paginate(config('constants.pagination.perPage'));

            return $this->success(200, [
                'feedbacks' => $feedbacks
            ]);
        } catch (\Throwable $e) {
            $this->errorLog($request, $e, 'LockerController show action', $user->id);
            return $this->error(400, "Feedbacks show failed.");
        }

    }
}
