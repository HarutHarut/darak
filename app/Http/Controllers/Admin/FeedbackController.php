<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use \Throwable;
use App\Models\Feedback;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Admin\Feedback\{Update, Delete};
use App\Http\Controllers\ApiController;

class FeedbackController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $admin = $request->user();
        try {
            $feedbacks = Feedback::query()
                ->with(['branch.business','user'])
                ->paginate(config('constants.pagination.perPage'));

            return $this->success(200, ['feedbacks' => $feedbacks]);
        } catch (\Throwable $e) {
            $this->errorLog($request, $e, 'Admin/FeedbackController index action', $admin->id);
            return $this->error(400, 'Something went wrong');
        }
    }

    public function update(Update $request, int $id): JsonResponse
    {
        $data = $request->validated();
        $admin = $request->user();

        try {
            DB::beginTransaction();

            $feedback = Feedback::query()->find($id);
            $feedback->update($data);
            $feedback->fresh();

            DB::commit();
            return $this->success(200, ['feedback' => $feedback], 'Feedback updated successfully.');
        } catch (\Throwable $e) {
            DB::rollback();
            $this->errorLog($request, $e, 'Admin/FeedbackController update action', $admin->id);
            return $this->error(400, 'Feedback update failed.');
        }
    }

    public function destroy(Delete $request, $id): JsonResponse
    {
        $admin = $request->user();
        try {
            DB::beginTransaction();

            $feedback = Feedback::query()->find($id);
            $feedback->delete();

            DB::commit();
            return $this->success(200, [], "Feedback deleted successfully");
        } catch (\Throwable $e) {
            DB::rollback();
            $this->errorLog($request, $e, 'Admin/FeedbackController destroy action', $admin->id);
            return $this->error(400, "Feedback deleting failed");
        }
    }
}
