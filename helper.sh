#!/usr/bin/env bash

OPERATION=$1
shift
ARGS=$*

case $OPERATION in
  'drupal') ddev exec drupal $ARGS;;
  'drush') ddev exec drush $ARGS;;
  'install')
    ddev exec drush site:install -y
    ddev exec drush config:set -y system.site uuid 002d5245-82cf-4586-b57f-5ab64ebcd877
    ddev exec drush config:delete -y shortcut.set.default
    ddev exec drush config:import -y --source=../config/sync
    ;;
  'phpcs')
    ddev exec ../vendor/bin/phpcs -v \
    --standard=Drupal,DrupalPractice \
    --extensions=php,module,inc,install,test,profile,theme,css,info,txt,md \
    --ignore=node_modules \
    {modules,themes}/custom
    $ARGS
    ;;
  'phpstan') ddev exec ../vendor/bin/phpstan analyse {modules,themes}/custom --level 7 $ARGS;;
  'refresh')
    ddev composer install
    ddev exec drupal cache:rebuild
    ddev exec drupal config:import
    ddev exec drupal update:execute
    ddev exec drupal cache:rebuild
    ;;
  'test')
    ./helper.sh phpstan
    ./helper.sh phpcs
    ;;
esac
