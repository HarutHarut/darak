<?php

namespace App\Exceptions;

use App\Models\ErrorLog;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Str;
use \Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->renderable(function (\Throwable $e, $request) {


            try {
                $user = $request->user();


                ErrorLog::query()->create([
                    'level' => 'error',
                    'category' => Str::replace(base_path(), '', $e->getFile()),
                    'user_id' => $user ? $user->id : null,
                    'user_ip' => $request->ip(),
                    'request_url' => $request->fullUrl(),
                    'message' => $e->getMessage(),
                    'server_ip' => null,
                    'request_id' => null,
                ]);
            } catch (\Throwable $exception ) {}
        });
    }
}
