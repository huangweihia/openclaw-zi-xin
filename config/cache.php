<?php

return [
    'default' => env('CACHE_DRIVER', 'redis'),
    'stores' => [
        'array' => ['driver' => 'array'],
        'database' => ['driver' => 'database', 'table' => 'cache'],
        'file' => ['driver' => 'file', 'path' => storage_path('framework/cache/data')],
        'memcached' => ['driver' => 'memcached', 'persistent_id' => env('MEMCACHED_PERSISTENT_ID')],
        'redis' => [
            'driver' => 'redis',
            'connection' => 'default',
            'lock_connection' => 'default',
        ],
    ],
    'prefix' => env('CACHE_PREFIX', 'laravel_cache_'),
];
?>
