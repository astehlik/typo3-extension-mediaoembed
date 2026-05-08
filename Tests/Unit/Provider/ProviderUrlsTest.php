<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Tests\Unit\Provider;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Sto\Mediaoembed\Provider\ProviderUrls;

#[CoversClass(ProviderUrls::class)]
class ProviderUrlsTest extends TestCase
{
    public function testGetUrls(): void
    {
        $urls = new ProviderUrls();
        $this->assertCount(66, $urls->getUrls());
    }
}
