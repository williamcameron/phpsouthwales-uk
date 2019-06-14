#!/usr/bin/env bash

OPERATION=$1
shift
ARGS=$*

case $OPERATION in
  'drush') ddev exec drush $ARGS;;

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
esac