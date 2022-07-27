<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ApiController;
use App\Models\ErrorLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use \Throwable;

class ErrorLogsController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $admin = $request->user();
        try {
            $errors = ErrorLog::query()->paginate(config('constants.pagination.perPage'));
            return $this->success(200, ['errors' => $errors]);
        } catch (\Throwable $e) {
            $this->errorLog($request, $e, $admin->id, 'Admin/ErrorLogsController index action');
            return $this->error(400, 'Something went wrong');
        }
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $admin = $request->user();
        try {
            $error = ErrorLog::query()->find($id);

            if($error)
                $error->delete();

            return $this->success(200, [], 'Error deleted successfully');
        } catch (\Throwable $e) {
            $this->errorLog($request, $e, 'Admin/ErrorLogsController destroy action', $admin->id);
            return $this->error(400, 'Something went wrong');
        }
    }

    public function truncate(Request $request): JsonResponse
    {
        $admin = $request->user();
        try {
            ErrorLog::query()->delete();
            return $this->success(200, [], 'Errors deleted successfully');
        } catch (\Throwable $e) {
            $this->errorLog($request, $e, 'Admin/ErrorLogsController truncate action', $admin->id);
            return $this->error(400, 'Something went wrong');
        }
    }
}
