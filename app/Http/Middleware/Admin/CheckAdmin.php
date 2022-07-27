<?php

namespace App\Http\Middleware\Admin;

use Closure;
use Illuminate\Http\Request;

class CheckAdmin
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
        if($request->user()->role->name !== 'admin') {
            return response()->json([
                'data' => [],
                'message' => 'You dont have permission for this request'
            ], 403);
        }
        return $next($request);
    }
}
