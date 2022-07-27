<?php

namespace App\Luglocker;

use Illuminate\Http\Request;

class General
{
    /**
     * @param Request $request
     * @return mixed
     */
    public static function resolveCurrency(Request $request){
//        $user = $request->user();
        $user = \Auth::guard('api')->user();
        return $user->currency ?? ($request->currency ?? env('DEFAULT_CURRENCY'));
    }

    public static function debugRes($res){
        return response()->json($res);
    }
}
