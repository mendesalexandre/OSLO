<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}
    public function boot(): void
    {
        // Modo de desenvolvimento
        if (app()->environment('local')) {
            DB::listen(function ($query) {
                logger()->info($query->sql);
            });
        }
    }
}
