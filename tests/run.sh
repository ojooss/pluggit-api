#!/usr/bin/env bash

#php ../vendor/bin/phpunit --bootstrap ../vendor/autoload.php .
echo quit | phpdbg -qrr ../vendor/bin/phpunit --bootstrap ../vendor/autoload.php  --coverage-html ./coverage --whitelist ../src "$@" .