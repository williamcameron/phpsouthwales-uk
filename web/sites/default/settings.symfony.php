<?php

$databases['default']['default'] = [
  'driver' => env('DATABASE_DRIVER'),
  'host' => env('DATABASE_HOST'),
  'database' => env('DATABASE_NAME'),
  'username' => env('DATABASE_USER'),
  'password' => env('DATABASE_PASSWORD'),
  'port' => env('DATABASE_PORT'),
  'prefix' => '',
  'namespace' => 'Drupal\\Core\\Database\\Driver\\mysql',
  'collation' => 'utf8mb4_general_ci',
];
