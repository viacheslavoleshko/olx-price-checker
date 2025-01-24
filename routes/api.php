<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\Api\v1\AuthController;
use App\Http\Controllers\Api\v1\CheckerController;
use App\Http\Controllers\Api\v1\VerifyEmailController;

Route::group([
    'prefix' => 'v1',
    'namespace' => 'App\Http\Controllers\Api\v1'
], function () {
    Route::group([
        'prefix' => 'auth',
    ], function () {
        Route::group(['middleware' => 'guest'], function () {
            Route::post('register', [AuthController::class, 'register'])->name('Auth.register');
            Route::post('login', [AuthController::class, 'login'])->name('Auth.login');
        });
    
        Route::group(['middleware' => 'auth:api'], function () {
            Route::post('logout', [AuthController::class, 'logout'])->name('Auth.logout');
        });
    });

    Route::group([
        'prefix' => 'email',
    ], function () {
        Route::get('/verify/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
            ->middleware(['signed', 'throttle:6,1'])
            ->name('verification.verify');

        Route::post('/verification-notification', function (Request $request): Response {
        $request->user()->sendEmailVerificationNotification();
            return response(['message' => 'Verification link sent!'], Response::HTTP_OK);
        })->middleware(['auth:api', 'throttle:6,1'])->name('verification.send');
    });

    Route::group([
        'prefix' => 'checker',
        'middleware' => ['auth:api' ,'verified'],
    ], function () {
        Route::get('/', [CheckerController::class, 'index'])->name('domains')->name('checkers');
        Route::post('/', [CheckerController::class, 'store'])->name('domains')->name('checkers.store');
    });
});