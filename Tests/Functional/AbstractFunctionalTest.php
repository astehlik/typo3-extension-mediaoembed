<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Tests\Functional;

use Nimut\TestingFramework\TestCase\FunctionalTestCase;

abstract class AbstractFunctionalTest extends FunctionalTestCase
{
    protected $testExtensionsToLoad = ['typo3conf/ext/mediaoembed'];
}
