<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\AuthController;


Route::prefix('v1')->group(function () {
    Route::post('/codigo_liberacao', [AuthController::class, 'enviarCodigo']);
    Route::post('/login', [AuthController::class, 'login']);
    
    Route::middleware('jwt.auth')->group(function () {
        Route::get('/notificacoes', [NotificationController::class, 'porCliente']);
        Route::post('/notificacoes', [NotificationController::class, 'store']);
        Route::put('/notificacoes/{notification}', [NotificationController::class, 'update']);
        Route::delete('/notificacoes/{notification}', [NotificationController::class, 'destroy']);
    });
});
