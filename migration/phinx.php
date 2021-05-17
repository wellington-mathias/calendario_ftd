<?php

require __DIR__.'/vendor/autoload.php';

return [
    'paths' => [
        'migrations' => [
            __DIR__ . '/database/migrations'
        ],
        'seeds' => [
            __DIR__ . '/database/seeds'
        ],
    ],
    'environments' => [
        'default_migration_table' => 'migrations',
        'default_database' => 'db',
        'db' => [
            'adapter' => 'mysql',
            'host' =>  getenv('DB_HOST_CALENDARIOFTD'),
            'name' =>  getenv('DB_DBNAME_CALENDARIOFTD'),
            'user' =>  getenv('DB_USER_CALENDARIOFTD'),
            'pass' =>  getenv('DB_PASSWORD_CALENDARIOFTD'),
            'chatset' => 'utf8',
            'collation' => 'utf8_unicode_ci'
        ]
    ]
];

