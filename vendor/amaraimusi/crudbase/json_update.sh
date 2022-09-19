#!/bin/sh
composer clear-cache
php composer.phar update
php composer.phar dump-autoload
php ./vendor/bin/phpunit
cmd /k