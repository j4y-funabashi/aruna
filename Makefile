test:
	docker-compose -f resources/docker/docker-compose-dev.yml run build vendor/bin/phpunit --config phpunit_ci.xml tests/unit

test_ci:
	docker-compose -f resources/docker/docker-compose-dev.yml run build vendor/bin/phpunit --config phpunit_ci.xml --coverage-html reports/coverage/unit tests/unit
	docker-compose -f resources/docker/docker-compose-dev.yml run build vendor/bin/phpunit --config phpunit_ci.xml --coverage-html reports/coverage/system tests/system

docker-build-dev:
	docker-compose -f resources/docker/docker-compose-dev.yml build

composer-install:
	docker-compose -f resources/docker/docker-compose-dev.yml run build composer install

run:
	docker-compose -f resources/docker/docker-compose-dev.yml up

deploy:
	docker-compose -f resources/docker/docker-compose-prod.yml build
	docker-compose -f resources/docker/docker-compose-prod.yml up
