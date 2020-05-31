#!/usr/bin/env bash

set -ev

echo "Running unit tests";

phpenv config-rm xdebug.ini

composer require ${TYPO3_VERSION}

.Build/bin/phpunit Tests/Unit/
