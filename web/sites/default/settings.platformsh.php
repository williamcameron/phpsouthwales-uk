<?php
/**
 * @file
 * Platform.sh settings.
 */

// Configure the database.
if (isset($_ENV['PLATFORM_RELATIONSHIPS'])) {
  $relationships = json_decode(base64_decode($_ENV['PLATFORM_RELATIONSHIPS']), TRUE);
  if (empty($databases['default']) && !empty($relationships)) {
    foreach ($relationships as $key => $relationship) {
      $drupal_key = ($key === 'database') ? 'default' : $key;
      foreach ($relationship as $instance) {
        if (empty($instance['scheme']) || ($instance['scheme'] !== 'mysql' && $instance['scheme'] !== 'pgsql')) {
          continue;
        }
        $database = [
          'driver' => $instance['scheme'],
          'database' => $instance['path'],
          'username' => $instance['username'],
          'password' => $instance['password'],
          'host' => $instance['host'],
          'port' => $instance['port'],
        ];

        if (!empty($instance['query']['compression'])) {
          $database['pdo'][PDO::MYSQL_ATTR_COMPRESS] = TRUE;
        }

        if (!empty($instance['query']['is_master'])) {
          $databases[$drupal_key]['default'] = $database;
        }
        else {
          $databases[$drupal_key]['replica'][] = $database;
        }
      }
    }
  }
}

if (isset($_ENV['PLATFORM_APP_DIR'])) {

  // Configure private and temporary file paths.
  if (!isset($settings['file_private_path'])) {
    $settings['file_private_path'] = $_ENV['PLATFORM_APP_DIR'] . '/private';
  }
  if (!isset($config['system.file']['path']['temporary'])) {
    $config['system.file']['path']['temporary'] = $_ENV['PLATFORM_APP_DIR'] . '/tmp';
  }

  // Configure the default PhpStorage and Twig template cache directories.
  if (!isset($settings['php_storage']['default'])) {
    $settings['php_storage']['default']['directory'] = $settings['file_private_path'];
  }
  if (!isset($settings['php_storage']['twig'])) {
    $settings['php_storage']['twig']['directory'] = $settings['file_private_path'];
  }

}

// Set trusted hosts based on Platform.sh routes.
if (isset($_ENV['PLATFORM_ROUTES']) && !isset($settings['trusted_host_patterns'])) {
  $routes = json_decode(base64_decode($_ENV['PLATFORM_ROUTES']), TRUE);
  $settings['trusted_host_patterns'] = [];
  foreach ($routes as $url => $route) {
    $host = parse_url($url, PHP_URL_HOST);
    if ($host !== FALSE && $route['type'] == 'upstream' && $route['upstream'] == $_ENV['PLATFORM_APPLICATION_NAME']) {
      $settings['trusted_host_patterns'][] = '^' . preg_quote($host) . '$';
    }
  }
  $settings['trusted_host_patterns'] = array_unique($settings['trusted_host_patterns']);
}

// Import variables prefixed with 'd8settings:' into $settings and 'd8config:'
// into $config.
if (isset($_ENV['PLATFORM_VARIABLES'])) {
  $variables = json_decode(base64_decode($_ENV['PLATFORM_VARIABLES']), TRUE);
  foreach ($variables as $name => $value) {
    // A variable named "d8settings:example-setting" will be saved in
    // $settings['example-setting'].
    if (strpos($name, 'd8settings:') === 0) {
      $settings[substr($name, 11)] = $value;
    }
    // A variable named "drupal:example-setting" will be saved in
    // $settings['example-setting'] (backwards compatibility).
    elseif (strpos($name, 'drupal:') === 0) {
      $settings[substr($name, 7)] = $value;
    }
    // A variable named "d8config:example-name:example-key" will be saved in
    // $config['example-name']['example-key'].
    elseif (strpos($name, 'd8config:') === 0 && substr_count($name, ':') >= 2) {
      list(, $config_key, $config_name) = explode(':', $name, 3);
      $config[$config_key][$config_name] = $value;
    }
    // A complex variable named "d8config:example-name" will be saved in
    // $config['example-name'].
    elseif (strpos($name, 'd8config:') === 0 && is_array($value)) {
      $config[substr($name, 9)] = $value;
    }
  }
}

// Set the project-specific entropy value, used for generating one-time
// keys and such.
if (isset($_ENV['PLATFORM_PROJECT_ENTROPY']) && empty($settings['hash_salt'])) {
  $settings['hash_salt'] = $_ENV['PLATFORM_PROJECT_ENTROPY'];
}

// Set the deployment identifier, which is used by some Drupal cache systems.
if (isset($_ENV['PLATFORM_TREE_ID']) && empty($settings['deployment_identifier'])) {
  $settings['deployment_identifier'] = $_ENV['PLATFORM_TREE_ID'];
}

// Set language negotiation per environment.
$host = strip_tags($_SERVER['HTTP_HOST']);
$exploded = explode(".", $host);
if ($exploded[0] == 'inviqa' && $exploded[1] == 'de') {
  unset($exploded[0]);
  unset($exploded[1]);
  $host_minus_de = implode($exploded, '.');
  $config['language.negotiation']['url']['domains']['en'] = $host_minus_de;
  $config['language.negotiation']['url']['domains']['de'] = $host;

  $settings['trusted_host_patterns'][] = $host;
  $settings['trusted_host_patterns'][] = $host_minus_de;
}
else {
  $config['language.negotiation']['url']['domains']['en'] = $host;
  $config['language.negotiation']['url']['domains']['de'] = 'inviqa.de.' . $host;

  $settings['trusted_host_patterns'][] = $host;
  $settings['trusted_host_patterns'][] = 'inviqa.de.' . $host;
}

// Environment (Branch) specific config.
if (!empty($_ENV['PLATFORM_BRANCH'])) {

  // @todo remove these tags once this feature has been tested, as they
  // should only be available on the master / production environment.
  $config['inviqa.settings']['gtm_id_override_en'] = 'GTM-NBN52P';
  $config['inviqa.settings']['gtm_id_override_de'] = 'GTM-MJGX72';

  $config['environment_indicator.indicator']['name'] = strtoupper($_ENV['PLATFORM_BRANCH']);
  $config['environment_indicator.indicator']['fg_color'] = '#FFF';

  switch ($_ENV['PLATFORM_BRANCH']) {

    case "develop":
      $config['environment_indicator.indicator']['bg_color'] = '#4e806b';;
      break;

    case "staging":
      $config['environment_indicator.indicator']['bg_color'] = '#a35f27';
      break;


    case "production":
      $config['environment_indicator.indicator']['bg_color'] = '#a3231c';
      $config['environment_indicator.indicator']['name'] = sprintf('PRODUCTION - %s', $settings['deployment_identifier']);

      // Hard code language negotiation for production environment.
      $config['language.negotiation']['url']['domains']['en'] = "inviqa.com";
      $config['language.negotiation']['url']['domains']['de'] = "inviqa.de";

      $settings['trusted_host_patterns'] = [
        '(www\.)?inviqa\.(com|de)$',
      ];

      $config['inviqa.settings']['gtm_id_override_en'] = 'GTM-NBN52P';
      $config['inviqa.settings']['gtm_id_override_de'] = 'GTM-MJGX72';

      $config['warden.settings'] = [
        'warden_server_host_path' => 'https://master-7rqtwti-23nobcg4e2ge4.eu.platform.sh',
        'warden_allow_requests' => TRUE,
        'warden_public_allow_ips' => '',
        'warden_preg_match_contrib' => '{^docroot\/modules\/contrib\/*}',
      ];
      break;

    case "master":
      $config['environment_indicator.indicator']['bg_color'] = '#a3231c';
      $config['environment_indicator.indicator']['name'] = sprintf('PRODUCTION - %s', $settings['deployment_identifier']);

      // Hard code language negotiation for production environment.
      $config['language.negotiation']['url']['domains']['en'] = "inviqa.com";
      $config['language.negotiation']['url']['domains']['de'] = "inviqa.de";

      $settings['trusted_host_patterns'] = [
        '(www\.)?inviqa\.(com|de)$',
      ];

      $config['inviqa.settings']['gtm_id_override_en'] = 'GTM-NBN52P';
      $config['inviqa.settings']['gtm_id_override_de'] = 'GTM-MJGX72';

      $config['warden.settings'] = [
        'warden_server_host_path' => 'https://master-7rqtwti-23nobcg4e2ge4.eu.platform.sh',
        'warden_allow_requests' => TRUE,
        'warden_public_allow_ips' => '',
        'warden_preg_match_contrib' => '{^docroot\/modules\/contrib\/*}',
      ];
      break;
  }

  if (strpos($_ENV['PLATFORM_BRANCH'], 'release/') !== FALSE) {
    $config['environment_indicator.indicator']['bg_color'] = '#a35f27';
  }

}
