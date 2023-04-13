<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('payment-callback', [\App\Http\Controllers\PaymentController::class, 'paymentCallback'])->name('payment.callback');
Route::get('/sitemap/branches.xml', [\App\Http\Controllers\SitemapController::class, 'branches']);
Route::get('/sitemap/cities.xml', [\App\Http\Controllers\SitemapController::class, 'cities']);
Route::get('/sitemap/blog.xml', [\App\Http\Controllers\SitemapController::class, 'blog']);
