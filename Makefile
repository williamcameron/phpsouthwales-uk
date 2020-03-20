SHELL := /bin/bash

init:
	symfony composer install
	cp .env.example .env
	make drupal-install
	make theme-build

drupal-install:
	cd web && \
	symfony php ../vendor/bin/drush site:install -y \
		--existing-config \
		--no-interaction && \
	symfony php ../vendor/bin/drush cache:rebuild && \
	symfony php ../vendor/bin/drush migrate:import --all \
	symfony php ../vendor/bin/drush core:cron

theme-build:
	cd web/themes/custom/phpsouthwales && \
	npm install && \
	npm run prod
