run: stop
	docker-compose -f resources/docker/docker-compose-dev.yml up
stop:
	docker-compose -f resources/docker/docker-compose-dev.yml stop

test:
	docker-compose -f resources/docker/docker-compose-dev.yml run aruna_build vendor/bin/phpunit --config phpunit_ci.xml tests/unit

test_ci:
	docker-compose -f resources/docker/docker-compose-dev.yml run aruna_build vendor/bin/phpunit --config phpunit_ci.xml --coverage-html reports/coverage/unit tests/unit
	docker-compose -f resources/docker/docker-compose-dev.yml run aruna_build vendor/bin/phpunit --config phpunit_ci.xml --coverage-html reports/coverage/system tests/system

dev-build:
	docker-compose -f resources/docker/docker-compose-dev.yml build

composer-install:
	docker-compose -f resources/docker/docker-compose-dev.yml run aruna_build composer install

deploy:
	docker-compose -f resources/docker/docker-compose-prod.yml aruna_build
	docker-compose -f resources/docker/docker-compose-prod.yml up
