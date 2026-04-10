<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\TicketApiController;
use App\Http\Controllers\Api\ProjetApiController;
use App\Http\Controllers\Api\ContratApiController;
use App\Http\Controllers\Api\UserApiController;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/tickets', [TicketApiController::class, 'index']);
    Route::post('/tickets', [TicketApiController::class, 'store']);
    Route::get('/tickets/{id}', [TicketApiController::class, 'show']);
    // celle la pas classique donc peut pas utiliser apiResource donc a la mano
    Route::post('/tickets/{id}/temps', [TicketApiController::class, 'storeTemps']);

    Route::apiResource('projets', ProjetApiController::class);
    Route::apiResource('contrats', ContratApiController::class);
    Route::apiResource('utilisateurs', UserApiController::class);
});
