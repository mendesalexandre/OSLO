<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'api' => [
            'name' => config('app.name'),
            'version' => '1.0.0',
            'environment' => app()->environment(),
            'status' => 'operacional'
        ],
        'server' => [
            'timestamp' => now()->toISOString(),
            'timezone' => config('app.timezone'),
            'locale' => app()->getLocale()
        ],
        'client' => [
            'ip' => request()->ip()
        ],
        'links' => [
            'documentation' => url('/docs'),
            'health_check' => url('/health'),
            'api_v1' => url('/api/v1')
        ]
    ]);
});
