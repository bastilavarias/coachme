<?php

use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\AvailabilityController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\AppointmentController;
use App\Http\Controllers\api\UserController;
use App\Http\Controllers\api\ServiceController;
use App\Http\Controllers\api\FeedbackController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('auth')->group(function () {
    Route::post('/oauth', [AuthController::class, 'doOauthAuthentication']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::middleware(['auth:api', 'token_binding'])->get('/refresh', [
        AuthController::class,
        'refresh',
    ]);
    Route::middleware(['auth:api'])->get('/logout', [
        AuthController::class,
        'logout',
    ]);
});

Route::middleware(['auth:api'])
    ->prefix('appointment')
    ->group(function () {
        Route::post('/', [AppointmentController::class, 'create']);
        Route::get('/', [AppointmentController::class, 'list']);
        Route::get('/available', [AppointmentController::class, 'available']);
        Route::put('/{appointmentID}', [
            AppointmentController::class,
            'update',
        ]);
    });

Route::middleware(['auth:api'])
    ->prefix('user')
    ->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::get('/{userID}', [UserController::class, 'show']);
        Route::put('/{userID}', [UserController::class, 'update']);
    });

Route::prefix('availability')->group(function () {
    Route::middleware(['auth:api', 'authorize:instructor'])->post('/', [
        AvailabilityController::class,
        'create',
    ]);
    Route::middleware(['auth:api', 'authorize:instructor'])->put(
        '/active/{availabilityID}',
        [AvailabilityController::class, 'changeStatus']
    );
    Route::middleware(['auth:api', 'authorize:instructor'])->put(
        '/{availabilityID}',
        [AvailabilityController::class, 'update']
    );
    Route::middleware(['auth:api', 'authorize:instructor,student'])->get('/', [
        AvailabilityController::class,
        'list',
    ]);
    Route::middleware(['auth:api', 'authorize:instructor'])->delete(
        '/{availabilityID}',
        [AvailabilityController::class, 'delete']
    );
});

Route::middleware(['auth:api', 'authorize:instructor'])
    ->prefix('service')
    ->group(function () {
        Route::post('/', [ServiceController::class, 'create']);
        Route::put('/{serviceID}', [ServiceController::class, 'update']);
        Route::get('/', [ServiceController::class, 'list']);
        Route::delete('/{serviceID}', [ServiceController::class, 'delete']);
    });

Route::middleware(['auth:api'])
    ->prefix('feedback')
    ->group(function () {
        Route::post('/', [FeedbackController::class, 'create']);
        Route::get('/', [FeedbackController::class, 'list']);
    });
