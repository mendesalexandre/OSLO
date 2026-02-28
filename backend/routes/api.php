<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuxiliarController;
use App\Http\Controllers\BancoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IndicadorPessoalController;
use App\Http\Controllers\IndisponibilidadeController;
use App\Http\Controllers\TipoTransacaoController;
use App\Http\Controllers\TransacaoController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // Token CSRF via JSON — necessário para SPAs cross-origin (sem proxy).
    // Inicia a sessão e retorna o token no corpo para evitar dependência de document.cookie.
    Route::get('csrf-token', function () {
        return response()->json(['token' => csrf_token()]);
    })->name('csrf-token');

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
        Route::post('indicador-pessoal/{id}/duplicar', [IndicadorPessoalController::class, 'duplicar'])->name('indicador-pessoal.duplicar');
        Route::apiResource('indicador-pessoal', IndicadorPessoalController::class)->only([
            'index', 'store', 'show', 'update', 'destroy',
        ]);

        // Indisponibilidade — rotas específicas antes do resource
        Route::get('indisponibilidades/cpf-cnpj/{cpfCnpj}', [IndisponibilidadeController::class, 'porCpfCnpj'])->name('indisponibilidades.por-cpf-cnpj');
        Route::post('indisponibilidades/{id}/cancelar', [IndisponibilidadeController::class, 'cancelar'])->name('indisponibilidades.cancelar');
        Route::apiResource('indisponibilidades', IndisponibilidadeController::class)->only([
            'index', 'store', 'show', 'update', 'destroy',
        ]);

        // Dashboard (Phase 08)
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
        Route::get('dashboard/eventos', [DashboardController::class, 'eventosPorMes'])->name('dashboard.eventos');

        // Catálogos de transação (Phase 06)
        Route::get('tipos-transacao', [TipoTransacaoController::class, 'index'])->name('tipos-transacao.index');
        Route::get('tipos-transacao/{tipo}/motivos', [TipoTransacaoController::class, 'porTipo'])->name('tipos-transacao.por-tipo');
        Route::get('bancos', [BancoController::class, 'index'])->name('bancos.index');

        // Transações (Phase 07)
        Route::get('transacoes/resumo', [TransacaoController::class, 'resumo'])->name('transacoes.resumo');
        Route::post('transacoes/{id}/confirmar', [TransacaoController::class, 'confirmar'])->name('transacoes.confirmar');
        Route::apiResource('transacoes', TransacaoController::class)->only([
            'index', 'store', 'show', 'update', 'destroy',
        ]);
    });

});
