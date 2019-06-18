#!/usr/bin/env bash

OPERATION=$1
shift
ARGS=$*

case $OPERATION in
  'drupal') ddev exec drupal $ARGS;;
  'drush') ddev exec drush $ARGS;;


  'phpcs') ddev exec ../vendor/bin/phpcs --standard=Drupal,DrupalPractice --extensions=php,module,inc,install,test,profile,theme,css,info,txt,md {modules,themes}/custom $ARGS;;

  'phpstan') ddev exec ../vendor/bin/phpstan analyse {modules,themes}/custom --level 7 $ARGS;;

  'refresh')
    # Install the site from scratch.
    ddev exec drush site:install -y

    # Reset the site uuid.
    ddev exec drush config:set -y system.site uuid 002d5245-82cf-4586-b57f-5ab64ebcd877

    # Delete the shortcut.set.default config.
    # This stops `config:import` from working.
    ddev exec drush config:delete -y shortcut.set.default

    # Import the initial config.
    ddev exec drush config:import -y --source=../config/sync
    ;;

    'test')
      ./helper.sh phpstan
      ./helper.sh phpcs
      ;;
esac
