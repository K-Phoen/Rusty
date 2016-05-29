tests: phpunit

phpunit:
	php ./vendor/bin/phpunit

rusty:
	php ./bin/rusty check --bootstrap-file=./vendor/autoload.php -v .

phar: box.phar
	composer install --no-dev -o
	php box.phar build

box.phar:
	curl -LSs https://box-project.github.io/box2/installer.php | php

.PHONY: tests phpunit rusty
