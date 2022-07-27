<?php


namespace App\Http\Controllers;

use App\Models\ErrorLog;
use Illuminate\Http\JsonResponse;

abstract class ApiController extends Controller
{

    public function success(int $status = 200, $data = [], string $message = ''): JsonResponse
    {
        $data['message'] = $message;
        return response()->json($data, $status);
    }

    public function error(int $status = 422, string $message = '', array $err = []): JsonResponse
    {
        $res = [
            'data' => $err,
            'message' => $message
        ];
        return response()->json($res, $status);
    }

    public function errorLog($request = null, $e, $category, $user_id = null): void
    {
        $user = $request->user() ?? null;
        ErrorLog::query()->create([
            'level' => 'error',
            'category' => $category,
            'user_id' => $user ? $user->id : $user_id,
            'user_ip' => $request->ip() ?? null,
            'request_url' => $request->fullUrl() ?? null,
            'message' => $e->getMessage() . ' ---- Line: ' . $e->getLine(),
            'server_ip' => null
        ]);
    }
}
