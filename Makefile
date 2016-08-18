test:
	vendor/bin/phpunit tests/unit

test_ci:
	vendor/bin/phpunit --config phpunit_ci.xml --coverage-html reports/coverage/unit tests/unit
	vendor/bin/phpunit --config phpunit_ci.xml tests/system

test_system:
	vendor/bin/phpunit --config phpunit_ci.xml tests/system
