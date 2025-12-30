<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\Admin\CategoryController;
use App\Http\Controllers\Api\Admin\SourceController;
use App\Http\Controllers\Api\Admin\ReportController;

Route::middleware('api')->group(function () {

    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {

        Route::get('/debug-auth', function () {
            return response()->json([
                'user' => auth()->user(),
                'id'   => auth()->id(),
                'guard' => auth()->getDefaultDriver(),
            ]);
        });

        Route::get('/me', fn(Request $request) => $request->user());
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::apiResource('transactions', TransactionController::class);

        Route::prefix('admin')->middleware('role:admin')->group(function () {
            Route::apiResource('categories', CategoryController::class);
            Route::apiResource('sources', SourceController::class);
            Route::get('reports', [ReportController::class, 'index']);
            Route::get('reports/charts', [ReportController::class, 'chart']);
            Route::get('reports/category-chart', [ReportController::class, 'categoryChart']);
        });
    });
});
