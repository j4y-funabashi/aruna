test:
	docker-compose -f resources/docker/docker-compose-dev.yml run app_daemon vendor/bin/phpunit --config phpunit_ci.xml --coverage-html reports/coverage/unit tests/unit

test_ci:
	docker-compose -f resources/docker/docker-compose-dev.yml run app_daemon vendor/bin/phpunit --config phpunit_ci.xml --coverage-html reports/coverage/unit tests/unit
	docker-compose -f resources/docker/docker-compose-dev.yml run app_daemon vendor/bin/phpunit --config phpunit_ci.xml tests/system

docker-build-dev:
	docker-compose -f resources/docker/docker-compose-dev.yml build

run:
	docker-compose -f resources/docker/docker-compose-dev.yml up
