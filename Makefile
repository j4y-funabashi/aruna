test:
	vendor/bin/phpunit tests/unit

test_ci:
	vendor/bin/phpunit --config phpunit_ci.xml --coverage-html build/coverage tests/unit
	vendor/bin/phpunit --config phpunit_ci.xml tests/system
