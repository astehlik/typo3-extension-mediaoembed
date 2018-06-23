<?php

namespace Sto\Mediaoembed\Tests\Unit\Provider;

use PHPUnit\Framework\TestCase;
use Sto\Mediaoembed\Provider\ProviderUrls;

class ProviderUrlsTest extends TestCase
{
    public function testGetUrls()
    {
        $urls = new ProviderUrls();
        $this->assertTrue(is_array($urls->getUrls()));
    }
}
