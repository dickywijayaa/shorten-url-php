<?php 

return [
    'default' => 'postgres',
    'connections' => [
        'mysql' => [
            'driver'    => 'mysql',
            'host'      => env('DB_HOST'),
            'database'  => env('DB_DATABASE'),
            'port'      => env('DB_PORT'),
            'username'  => env('DB_USERNAME'),
            'password'  => env('DB_PASSWORD'),
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
        ],
        'postgres' => [
            'driver'   => 'pgsql',
            'host'     => env('DB_PG_HOST'),
            'database' => env('DB_PG_DATABASE'),
            'port'     => env('DB_PG_PORT'),
            'username' => env('DB_PG_USERNAME'),
            'password' => env('DB_PG_PASSWORD', ''),
            'charset'  => 'utf8',
            'prefix'   => '',
            'schema'   => 'public'
        ]
    ]
];