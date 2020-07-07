DB_DIR := tools/assets/development
DB_NAME := drupal.sql.gz
DB_PATH := $(DB_DIR)/$(DB_NAME)
RUN_NODE := docker-compose run --rm node
RUN_PHP := docker-compose run --rm php
THEME_PATH := web/themes/custom/phpsouthwales

.PHONY: default console copy-required-files start stop test-unit theme-build install-drupal

default: copy-required-files start vendor theme-build install-drupal test-unit

console:
	docker-compose run --rm php bash

copy-required-files:
	cp .docker.env.example .docker.env
	cp .env.example .env
	cp web/sites/example.settings.local.php web/sites/default/settings.local.php

install-drupal:
	bin/drush.sh site:install -y config_installer --account-pass=admin123
	bin/drush.sh migrate:import --all
	bin/drush.sh sql:dump --gzip > $(DB_PATH)

start:
	docker-compose up -d

stop:
	docker-compose down --remove-orphans

test-unit:
	$(RUN_PHP) vendor/bin/phpunit web/modules/custom

theme-build: $(THEME_PATH)/package.json $(THEME_PATH)/package-lock.json
	$(RUN_NODE) npm install
	$(RUN_NODE) npm run dev

vendor: composer.json composer.lock
	bin/composer.sh validate --strict
	bin/composer.sh install
