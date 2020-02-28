<?php

$databases['default']['default'] = [
  'driver' => getenv('DATABASE_DRIVER'),
  'host' => getenv('DATABASE_HOST'),
  'database' => getenv('DATABASE_NAME'),
  'username' => getenv('DATABASE_USER'),
  'password' => getenv('DATABASE_PASSWORD'),
  'port' => getenv('DATABASE_PORT'),
  'prefix' => '',
  'namespace' => 'Drupal\\Core\\Database\\Driver\\mysql',
  'collation' => 'utf8mb4_general_ci',
];
