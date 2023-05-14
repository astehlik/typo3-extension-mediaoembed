<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Tests\Unit\Provider;

use PHPUnit\Framework\TestCase;
use Sto\Mediaoembed\Provider\ProviderUrls;

class ProviderUrlsTest extends TestCase
{
    public function testGetUrls(): void
    {
        $urls = new ProviderUrls();
        self::assertTrue(is_array($urls->getUrls()));
    }
}
