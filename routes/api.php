<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\Admin\CategoryController;
use App\Http\Controllers\Api\Admin\SourceController;
use App\Http\Controllers\Api\Admin\ReportController;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/

Route::post('/login', [AuthController::class, 'login'])->name('api.login');

/*
|--------------------------------------------------------------------------
| PROTECTED ROUTES (AUTHENTICATED)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum'])->group(function () {

    // Auth
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');
    Route::get('/me', fn() => auth()->user())->name('api.me');

    /*
    |--------------------------------------------------------------------------
    | TRANSACTIONS (ADMIN & EMPLOYEE)
    |--------------------------------------------------------------------------
    */
    Route::apiResource('transactions', TransactionController::class)
        ->only(['index', 'store', 'show']);

    /*
    |--------------------------------------------------------------------------
    | ADMIN ONLY
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin')
        ->middleware('role:admin')
        ->group(function () {

            Route::apiResource('categories', CategoryController::class);
            Route::apiResource('sources', SourceController::class);

            // Financial Report
            Route::get('/reports', [ReportController::class, 'index'])
                ->name('admin.reports');
            Route::get('/reports/charts', [ReportController::class, 'charts']);
        });
});
