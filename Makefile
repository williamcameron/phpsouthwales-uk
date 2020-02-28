SHELL := /bin/bash

build-theme:
	cd web/themes/custom/phpsouthwales && \
	npm install && \
	npm run prod
