<?php

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\ServiceProvider;

return [
    'name' => env('APP_NAME', 'AI 副业情报局'),
    'env' => env('APP_ENV', 'production'),
    'debug' => (bool) env('APP_DEBUG', false),
    'url' => env('APP_URL', 'http://localhost'),
    /*
     * 服务端自调用（如 Filament 调本应用 API）时使用，避免 APP_URL 为宿主机端口
     *（如 http://localhost:8081）时在 Docker 容器内连错端口。容器内 Web 一般为 80。
     * php artisan serve 时可设为 http://127.0.0.1:8000
     */
    'internal_url' => env('APP_INTERNAL_URL', 'http://127.0.0.1'),
    'timezone' => 'Asia/Shanghai',
    'locale' => 'zh_CN',
    'fallback_locale' => 'en',
    'faker_locale' => 'zh_CN',
    'key' => env('APP_KEY'),
    'cipher' => 'AES-256-CBC',
    'maintenance' => ['driver' => 'file'],
    'providers' => ServiceProvider::defaultProviders()->merge([
        App\Providers\AppServiceProvider::class,
        App\Providers\Filament\AdminPanelProvider::class,
        App\Providers\RouteServiceProvider::class,
    ])->toArray(),
    'aliases' => Facade::defaultAliases()->merge([])->toArray()
];