#!/usr/bin/env bash

set -ev

echo "Running phpcs"

phpenv config-rm xdebug.ini

composer create-project --no-dev squizlabs/php_codesniffer:^3.3 codesniffer

cd codesniffer

composer require --update-no-dev de-swebhosting/php-codestyle:dev-master

cd ..

./codesniffer/bin/phpcs --config-set installed_paths $PWD/codesniffer/vendor/de-swebhosting/php-codestyle/PhpCodeSniffer

./codesniffer/bin/phpcs --standard=PSRDefault Classes Configuration/TCA Tests ext_*.php
