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
    ddev exec drush site:install -y
    ddev exec drush config:set -y system.site uuid 002d5245-82cf-4586-b57f-5ab64ebcd877
    ddev exec drush config:delete -y shortcut.set.default
    ddev exec drush config:import -y --source=../config/sync
    ;;
  'test')
    ./helper.sh phpstan
    ./helper.sh phpcs
    ;;
esac
