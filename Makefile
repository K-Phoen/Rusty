tests: phpunit

phpunit:
	php ./vendor/bin/phpunit

rusty:
	php ./bin/rusty check --bootstrap-file=./vendor/autoload.php -v .
