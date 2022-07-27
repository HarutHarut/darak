<?php

namespace App\Http\Middleware\Api;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckBusinessOrAdmin
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
        $user = Auth::user();

        if ($user->role->name == 'business_owner' || $request->user()->role->name == 'admin') {
                return $next($request);
        }
        $res = [
            'data' => [],
            'message' => 'You dont have permission for this request.'
        ];
        return response()->json($res, 403);
    }
}
