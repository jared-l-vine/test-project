<?php
return [
    'paths' => [
        'migrations' => __DIR__ . '/db/migrations',
        'seeds' => __DIR__ . '/db/seeds',
    ],

    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_database' => 'development',
        'development' => [
            'adapter' => 'mysql',
            'host' => 'localhost',
            'name' => 'pinpoint',
            'user' => 'pinpoint',
            'pass' => 'pinpoint',
            'port' => 3306,
            'charset' => 'utf8',
        ]
    ]
];
