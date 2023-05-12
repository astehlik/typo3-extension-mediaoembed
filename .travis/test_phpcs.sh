#!/usr/bin/env bash

set -ev

echo "Running phpcs"

if [[ -x "$(command -v phpenv)" ]]; then
    phpenv config-rm xdebug.ini
fi

if [[ ! -d "codesniffer" ]]; then
    composer create-project --no-dev squizlabs/php_codesniffer:^3.3 codesniffer
fi

if [[ ! -d "codesniffer/vendor/de-swebhosting" ]]; then
    cd codesniffer
    composer require --update-no-dev de-swebhosting/php-codestyle:dev-master
    cd ..
fi

./codesniffer/bin/phpcs --config-set installed_paths $PWD/codesniffer/vendor/de-swebhosting/php-codestyle/PhpCodeSniffer,$PWD/Tests/CodeSniffer

./codesniffer/bin/phpcs --standard=PSRMediaoembed --extensions=php Classes Configuration/TCA Tests ext_*.php
