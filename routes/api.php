<?php


use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\StaticPageController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Api\BusinessController;
use App\Http\Controllers\Api\BranchController;
use App\Http\Controllers\Api\PriceController;
use App\Http\Controllers\Api\SizeController;
use App\Http\Controllers\Api\LockerController;
use App\Http\Controllers\Api\FeedbackController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\SocialController;
use App\Http\Controllers\Api\TimeController;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\CurrencyController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\PaymentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
// Integrated routes

Route::middleware('guest')->group(function () {
    Route::post('register', [AuthController::class, 'register'])->name('register');
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('/socLogin/{provider}', [AuthController::class, 'socLogin'])->name('socLogin');


    // refresh/token is not Integrated routes
    Route::post('refresh/token', [AuthController::class, 'refreshToken'])->name('refreshToken');
    Route::post('/reset', [AuthController::class, 'resetPassword'])->name('reset.password');
    Route::post('/forgot', [AuthController::class, 'forgotPassword'])->name('forgot.password');
    Route::post('/set-new-password', [AuthController::class, 'setNewPassword'])->name('set.new.password');
    Route::get('/email-verify', [AuthController::class, 'emailVerify'])->name('email.verify');

});


Route::prefix('blogs')->group(function () {
    Route::get('/', [BlogController::class, 'all'])->name('blog.all');
    Route::get('/top', [BlogController::class, 'topBlogs'])->name('blog.top');
    Route::get('/{slug}', [BlogController::class, 'show'])->name('blog.show');
});

Route::prefix('cities')->group(function () {
    Route::get('/', [CityController::class, 'index'])->name('cities.all');
    Route::get('/top', [CityController::class, 'topCities'])->name('cities.top');
    Route::get('/{id}', [CityController::class, 'show'])->name('cities.show');
});
Route::get('/city-logo', [CityController::class, 'logo'])->name('cities.logo');

Route::prefix('branches')->group(function () {
    Route::get('/recommended', [BranchController::class, 'recommended'])->name('branch.recommended');
    Route::get('/map', [BranchController::class, 'map'])->name('branch.map');
    Route::get('/user/{slug}', [BranchController::class, 'singleBranch'])->name('single.branch.show');
    Route::get('/user/getBranchMinPrice/{slug}', [BranchController::class, 'getBranchMinPrice']);
    Route::post('/lockers', [BranchController::class, 'searchLockers'])->name('search.lockers');
    Route::get('/min-price/{slug}', [BranchController::class, 'minPrice'])->name('search.minPrice');
});

Route::get('/currency', [CurrencyController::class, 'all'])->name('currency.all');
Route::post('/contacts', [ContactController::class, 'create'])->name('contact.create');
Route::get('/lockers/map', [LockerController::class, 'lockersMap'])->name('search.branches');

Route::get('/static-page', [StaticPageController::class, 'show'])->name('staticPage.show');
// End Integrated routes


Route::middleware(['auth:api'])->group(function () {
    Route::post('/newPay/{id}', [PaymentController::class, "newPay"])->name('payment');

    Route::get('/invoices', [InvoiceController::class, 'getAll'])->name('invoices');
    Route::get('/invoices/datas', [InvoiceController::class, 'datas'])->name('invoices.datas');
    Route::post('/invoices/change-status', [InvoiceController::class, 'changeStatus'])->name('invoice.changeStatus');

    Route::get('/business', [BusinessController::class, 'get'])->name('business');

    Route::get('/pay/{invoice_id}', [PaymentController::class, 'pay'])->name('pay');
//    Route::get('/payment-callback', [PaymentController::class, 'paymentCallback'])->name('payment.callback');
    Route::post('/cancel-order', [BookingController::class, 'cancelOrder'])->name('cancel.order');
    Route::get('/cancel-booking', [AuthController::class, 'cancelBooking'])->name('email.cancel-booking');


    Route::prefix('branches')->group(function () {
        Route::post('/lockers/booking', [BranchController::class, 'searchLockers'])->name('search.auth.lockers');
    });

    Route::prefix('orders')->group(function () {
        Route::post('/order', [OrderController::class, 'getOrders'])->name('get.orders');
        Route::post('/single/{book_number}', [OrderController::class, 'orderSingle'])->name('orders.single');
        Route::get('/search', [OrderController::class, 'search'])->name('orders.search');
    });

    Route::prefix('blog')->group(function () {
        Route::post('create', [BlogController::class, 'create'])->name('blog.create');
        Route::put('update', [BlogController::class, 'update'])->name('blog.update');
        Route::delete('{id}', [BlogController::class, 'deleteBlog'])->name('blog.delete');
        Route::delete('translation/{id}', [BlogController::class, 'deleteTranslation'])->name('blog.translation.delete');
    });

    Route::get('user', [AuthController::class, 'user'])->name('user');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('user/currency', [UserController::class, 'userCurrency'])->name('user.currency');
    Route::post('/user/change-status', [UserController::class, 'changeStatus'])->name('search.changeStatus');



    Route::post('resend/verification/email', [AuthController::class, 'resendVerification'])
        ->middleware('block')->name('verification.send');

    Route::post('email/verify/{hash}', [AuthController::class, 'verify'])
        ->middleware('block')->name('verification.verify');

    Route::get('verification/notice', [AuthController::class, 'verificationNotice'])->name('verification.notice');

    Route::middleware(['verified', 'block'])->group(function () {
        Route::middleware('businessOrAdmin')->group(function () {
            // Integrated routes
            Route::prefix('feedbacks')->group(function () {
                Route::get('/', [FeedbackController::class, 'all'])->name('feedbacks.all');
                Route::delete('/{id}', [FeedbackController::class, 'delete'])->name('feedbacks.delete');
            });
            Route::post('/user/search', [UserController::class, 'searchUser'])->name('user.search');
            Route::post('/branchesByBusinessId', [BranchController::class, 'branchesByBusinessId'])->name('branch.byBusinessId');
            Route::post('/searchBranch', [BranchController::class, 'searchBranches'])->name('search.branches');
            Route::prefix('/branches')->group(function () {
                Route::post('/create', [BranchController::class, 'create'])->name('branch.create');
                Route::post('/update', [BranchController::class, 'update'])->name('branch.update');
                Route::post('/update/media', [BranchController::class, 'updateMedia'])->name('branch.update.media');
                Route::get('/list', [BranchController::class, 'all'])->name('branch.all');
                Route::get('/', [BranchController::class, 'index'])->name('branch.index');
                Route::post('/change-status', [BranchController::class, 'changeStatus'])->name('businesses.changeStatus');
            });

            Route::prefix('sizes')->group(function () {
                Route::get('/', [SizeController::class, 'all'])->name('size.all');
                Route::get('/list', [SizeController::class, 'list'])->name('size.list');
                Route::post('/create', [SizeController::class, 'create'])->name('size.create');
                Route::delete('/{id}', [SizeController::class, 'delete'])->name('size.delete');
                Route::put('/update', [SizeController::class, 'update'])->name('size.update');
            });


            Route::prefix('lockers')->group(function () {
                Route::get('/', [LockerController::class, 'all'])->name('locker.all');
                Route::get('/{id}', [LockerController::class, 'show'])->name('locker.show');
                Route::post('create', [LockerController::class, 'create'])->name('locker.create');
                Route::put('update', [LockerController::class, 'update'])->name('locker.update');
                Route::post('/updateGraph', [LockerController::class, 'updateGraph'])->name('update.graph');
                Route::post('/close-with-date-range', [LockerController::class, 'closeDaysWithDateRange'])->name('update.graph');
            });
            // End Integrated routes


            Route::prefix('business')->group(function () {
//                Route::post('create', [BusinessController::class,'create'])->name('business.create');
                Route::put('update', [BusinessController::class, 'update'])->name('business.update');
                Route::get('list', [BusinessController::class, 'list'])->name('business.list');
                Route::get('/edit/{id}', [BusinessController::class, 'getBusiness'])->name('branch.getBusiness');////

            });


            Route::prefix('price')->group(function () {
                Route::get('/', [PriceController::class, 'all'])->name('price.all');
                Route::post('create', [PriceController::class, 'create'])->name('price.create');
                Route::put('update', [PriceController::class, 'update'])->name('price.update');
            });


            Route::prefix('social/url')->group(function () {
                Route::post('create', [SocialController::class, 'create'])->name('social.url.create');
                Route::put('update', [SocialController::class, 'update'])->name('social.url.update');
            });

            Route::prefix('open/times')->group(function () {
                Route::post('/', [TimeController::class, 'createOpenTime'])->name('open.time.create');
                Route::post('update', [TimeController::class, 'updateOpenTime'])->name('open.time.update');
            });

            Route::prefix('close/time')->group(function () {
                Route::post('create', [TimeController::class, 'createCloseTime'])->name('close.time.create');
                Route::put('update', [TimeController::class, 'updateCloseTime'])->name('close.time.update');
            });


            Route::prefix('business/book')->group(function () {
                Route::post('/cancel', [BookingController::class, 'bookCancelByBusiness'])->name('book.cancelByBusiness');
                Route::get('/cancel/check', [BookingController::class, 'checkCancelByBusiness'])->name('book.cancel.checkByBusiness');
            });

            Route::post('dashboard', [DashboardController::class, 'index'])->name('dashboard');
            Route::post('dashboard/orders', [DashboardController::class, 'orders'])->name('dashboard');
            Route::post('dashboard/invoices', [DashboardController::class, 'invoices'])->name('invoices');
        });


        Route::prefix('user')->group(function () {
            Route::prefix('feedback')->group(function () {
                Route::post('create', [FeedbackController::class, 'create'])->name('locker.create');
                Route::get('/{id}', [FeedbackController::class, 'feedbacksShow'])->name('feedback.show');
            });
        });


        Route::post('/media/delete', [MediaController::class, 'delete']);

        Route::post('/profile/edit', [ProfileController::class, 'edit']);
        Route::post('/profile/update', [ProfileController::class, 'update']);
        Route::post('/profile/change-password', [ProfileController::class, 'changePassword']);

        Route::prefix('book')->group(function () {
            Route::post('/', [BookingController::class, 'book'])->name('book.create');
            Route::get('check', [BookingController::class, 'check'])->name('book.check');
            Route::get('calculate', [BookingController::class, 'calculateForBusiness'])->name('book.calculate');
            Route::post('/cancel', [BookingController::class, 'bookCancelByUser'])->name('book.cancelByUser');
            Route::get('/cancel/check', [BookingController::class, 'checkCancelByUser'])->name('book.cancel.checkByUser');
        });
    });
    Route::get('branches/{slug}', [BranchController::class, 'show'])->name('branch.show');

});
