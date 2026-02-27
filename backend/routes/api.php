<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuxiliarController;
use App\Http\Controllers\IndicadorPessoalController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // Autenticação
    Route::prefix('auth')->name('auth.')->group(function () {
        Route::post('login', [AuthController::class, 'login'])->name('login');

        Route::middleware('auth:sanctum')->group(function () {
            Route::post('logout', [AuthController::class, 'logout'])->name('logout');
            Route::get('me', [AuthController::class, 'me'])->name('me');
            Route::post('refresh', [AuthController::class, 'refresh'])->name('refresh');
        });
    });

    Route::middleware('auth:sanctum')->group(function () {
        // Tabelas auxiliares
        Route::get('auxiliares/{tabela}', [AuxiliarController::class, 'index'])->name('auxiliares.index');

        // Indicador Pessoal — rotas específicas antes do resource para evitar conflito com {id}
        Route::get('indicador-pessoal/busca', [IndicadorPessoalController::class, 'busca'])->name('indicador-pessoal.busca');
        Route::get('indicador-pessoal/{cpfCnpj}/versoes', [IndicadorPessoalController::class, 'versoes'])->name('indicador-pessoal.versoes');
        Route::apiResource('indicador-pessoal', IndicadorPessoalController::class)->only([
            'index', 'store', 'show', 'update', 'destroy',
        ]);
    });

});
