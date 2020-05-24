install:
	composer install
test:
	composer run-script phpunit tests
test_coverage:
	composer phpunit -- --coverage-clover ./clover.xml tests