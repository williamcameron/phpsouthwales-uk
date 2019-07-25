<?php

$databases['default']['default'] = [
  'driver' => 'mysql',
  'host' => env('MYSQL_HOST', 'db'),
  'database' => env('MYSQL_DATABASE', 'default'),
  'username' => env('MYSQL_USER', 'user'),
  'password' => env('MYSQL_PASSWORD', 'user'),
  'port' => 3306,
  'prefix' => '',
];

$settings['hash_salt'] = env('DRUPAL_SALT');
