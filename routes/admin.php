<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\{BlogController,
    BlogTranslationsController,
    BookingController,
    CityController,
    FeedbackController,
    LockerController,
    PriceController,
    SizeController,
    TimeController,
    UserController,
    BusinessController,
    BranchController,
    ErrorLogsController
};
use  App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Admin\StaticPageController;

Route::apiResource('users', UserController::class)
    ->only(['index', 'show',  'destroy', 'store'])
    ->names('users');


/* - - - Businesses  - - -  */
Route::apiResource('businesses', BusinessController::class)
    ->only(['index', 'show', 'update'])
    ->names('businesses');

//Route::get('businesses', [StaticPageController::class,'index'])->name('index');
Route::prefix('businesses')->group(function () {
    Route::post('/change_status/{business}', [BusinessController::class, 'changeStatus'])->name('businesses.change_status');
    Route::post('/edit/{id}', [BusinessController::class, 'editBusiness'])->name('businesses.edit');
});


/* - - - Branches  - - -  */
Route::apiResource('branches', BranchController::class)
    ->only(['show', 'update', 'index', 'destroy'])
    ->names('branches');

Route::prefix('branches')->group(function () {
    Route::post('/change_status/{branch}', [BranchController::class, 'changeStatus'])->name('branches.change_status');
    Route::post('/recommended-status/{id}', [BranchController::class, 'recommendedStatus'])->name('branches.recommendedStatus');
    Route::post('/working-status/{id}', [BranchController::class, 'workingStatus'])->name('branches.workingSstatus');
});


/* - - - Bookings  - - -  */
Route::apiResource('bookings', BookingController::class)
    ->only(['index', 'update','show'])
    ->names('bookings');
Route::post('bookings/cancel', [BookingController::class, 'cancel'])->name('booking.cancel');

Route::post('business/blocked', [OrderController::class,'blockedBusinessOrUserStatus'])->name('business.blocked');
//Route::post('user/blocked', [UserController::class,'changeUserStatus'])->name('user.blocked');


/* - - - Static Pages  - - -  */
Route::get('statics', [StaticPageController::class, 'index'])->name('index');
Route::put('statics/update/{slug}', [StaticPageController::class,'update'])->name('update');





/* - - - Blogs  - - -  */
Route::apiResource('blogs', BlogController::class)
    ->only(['index', 'store', 'update', 'destroy'])
    ->names('blogs');


Route::apiResource('blog_translations', BlogTranslationsController::class)
    ->only(['destroy'])
    ->names('blog_translations');


/* - - - Error Logs  - - -  */
Route::apiResource('error_logs', ErrorLogsController::class)
    ->only(['index', 'destroy'])
    ->names('error_logs');

Route::prefix('error_logs')->group(function () {
    Route::post('truncate', [ErrorLogsController::class, 'truncate'])->name('error_logs.truncate');
});


/* - - - Sizes  - - -  */
Route::apiResource('sizes', SizeController::class)
    ->only(['store', 'update','index'])
    ->names('sizes');


/* - - - Lockers  - - -  */
Route::apiResource('lockers', LockerController::class)
    ->only(['index', 'store', 'update'])
    ->names('lockers');


/* - - - Opening and Closing Times  - - -  */
Route::prefix('time')->group(function () {
    Route::post('open', [TimeController::class, 'storeOpenTime'])->name('time.storeOpenTime');
    Route::put('open', [TimeController::class, 'updateOpenTime'])->name('time.updateOpenTime');

    Route::post('close', [TimeController::class, 'storeCloseTime'])->name('time.storeCloseTime');
    Route::put('close', [TimeController::class, 'updateCloseTime'])->name('time.updateCloseTime');
});


/* - - - Price Packages  - - -  */
Route::prefix('prices')->group(function () {
    Route::get('/', [PriceController::class, 'index'])->name('prices.index');
    Route::post('/', [PriceController::class, 'store'])->name('prices.store');
    Route::put('/', [PriceController::class, 'update'])->name('prices.update');
});


/* - - - Cities  - - -  */
Route::apiResource('cities', CityController::class)
    ->only(['index', 'show', 'store', 'update'])
    ->names('cities');


/* - - - Feedbacks  - - -  */
Route::apiResource('feedbacks', FeedbackController::class)
    ->only(['index', 'update', 'destroy'])
    ->names('feedbacks');

Route::get('/user/{id}', [UserController::class, 'getUser'])->name('user');

/* - - - Users  - - -  */
Route::prefix('users')->group(function () {
    Route::post('/update', [UserController::class, 'update'])->name('users.update');
});
