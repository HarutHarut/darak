<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StaticPages;
use Illuminate\Http\Request;

class StaticPageController extends Controller
{
    public function show(Request $request) {
        $staticPage = StaticPages::query()->where('slug', '=', $request->input('name'))->first();
        return response()->json($staticPage);
    }
}
