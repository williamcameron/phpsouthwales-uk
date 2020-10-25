SHELL := /bin/bash -e -o pipefail

asset_dir := tools/assets/development
db_name := phpsouthwales.sql.gz
db_path := ${asset_dir}/${db_name}
theme_path := web/themes/custom/phpsouthwales

.PHONY: *

clean: ${theme_path}/build ${theme_path}/node_modules
	rm -fr vendor
	rm -fr ${theme_path}/{build,node_modules}

config-export:
	drush config:export -y

features-export:
	drush features:export -y \
		phpsw_event

drupal-install: vendor web/sites/default/settings.php
	drush site:install -y \
		--existing-config \
		--no-interaction && \
	drush cache:rebuild

drupal-post-install: web/sites/default/settings.php
	drush migrate:import --all
	drush core:cron

init: .env.example
	make vendor
	cp .env.example .env

pull-from-prod:
	# Download a fresh database from Platform.sh.
	platform db:dump -e master --gzip -f ${db_path}

refresh:
	# - Re-import and sanitise the database.
	# - Run any database updates.
	# - Rebuild the Drupal cache.
	stat ${db_path} || exit 1
	drush sql-drop -y
	zcat < ${db_path} | drush sql-cli
	drush sql-sanitize -y --sanitize-password=password
	drush updatedb -y
	drush cache-rebuild

test-phpcs:
	php vendor/bin/phpcs -v \
		--standard=Drupal \
		--extensions=php,module,inc,install,test,profile,theme,pcss,info,txt,md \
		--ignore=node_modules,*/tests/* \
		web/modules/custom \
		web/themes/custom

test-phpstan:
	php vendor/bin/phpstan analyze

test-phpunit:
	php vendor/bin/phpunit web/modules/custom --verbose --testdox

test: test-phpcs test-phpstan test-phpunit

theme-build: ${theme_path}/package.json ${theme_path}/package-lock.json
	cd ${theme_path} && \
	npm install && \
	npm run prod
	drush cache:rebuild

vendor: composer.json
	composer validate
	composer install
