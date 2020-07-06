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
	bin/drush.sh config:export -y

features-export:
	bin/drush.sh features:export -y \
		phpsw_event

drupal-install: vendor web/sites/default/settings.php
	bin/drush.sh site:install -y \
		--existing-config \
		--no-interaction && \
	bin/drush.sh cache:rebuild

drupal-post-install: web/sites/default/settings.php
	bin/drush.sh migrate:import --all
	bin/drush.sh core:cron

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
	bin/drush.sh sql-drop -y
	zcat < ${db_path} | bin/drush.sh sql-cli
	bin/drush.sh sql-sanitize -y --sanitize-password=password
	bin/drush.sh updatedb -y
	bin/drush.sh cache-rebuild

test-phpcs:
	symfony php vendor/bin/phpcs -v \
		--standard=Drupal \
		--extensions=php,module,inc,install,test,profile,theme,pcss,info,txt,md \
		--ignore=node_modules,*/tests/* \
		web/modules/custom \
		web/themes/custom

test-phpstan:
	symfony php vendor/bin/phpstan analyze

test-phpunit:
	symfony php vendor/bin/phpunit web/modules/custom --colors=always --testdox

test: test-phpcs test-phpstan test-phpunit

theme-build: ${theme_path}/package.json ${theme_path}/package-lock.json
	cd ${theme_path} && \
	npm install && \
	npm run prod
	bin/drush.sh cache:rebuild

vendor: composer.json
	symfony composer validate
	symfony composer install
