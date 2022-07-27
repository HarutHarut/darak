<?php

namespace App\Http\Middleware\Api;

use Closure;
use Illuminate\Http\Request;

class CheckUserBlock
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->user()->status == config('constants.user_status.blocked')){

            $res = [
                'data' => [],
                'message' => 'Your account has been blocked by admin.'
            ];

            return response()->json($res, 403);
        }
        return $next($request);
    }
}
