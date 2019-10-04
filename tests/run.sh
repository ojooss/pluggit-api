#!/usr/bin/env bash

# goto this scripts dir
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"
cd $DIR

# call phpunit with phpdbg enabled for coverage
echo quit | phpdbg -qrr ../vendor/bin/phpunit --bootstrap ../vendor/autoload.php  --coverage-html ./coverage --whitelist ../src "$@" .

# want no coverage? can call like this:
#php ../vendor/bin/phpunit --bootstrap ../vendor/autoload.php .