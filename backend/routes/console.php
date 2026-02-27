<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Limpeza de registros de auditoria com mais de 365 dias
Schedule::call(function () {
    DB::statement('SELECT auditoria.fn_limpar_registros_antigos(365)');
})->daily()
  ->at('03:00')
  ->name('auditoria:limpar-registros-antigos')
  ->withoutOverlapping();
