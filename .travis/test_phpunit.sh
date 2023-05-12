#!/usr/bin/env bash

set -ev

echo "Running unit tests";

phpenv config-rm xdebug.ini

composer require ${TYPO3_VERSION}

# Disable E_DEPRECATED errors to prevent phpunit failure
# because of too old phpunit version for PHP 7.4 throwing
# Function ReflectionType::__toString() is deprecated
.Build/bin/phpunit -d error_reporting=24575 Tests/Unit/
