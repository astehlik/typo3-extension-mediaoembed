<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Tests\Unit\Provider;

use PHPUnit\Framework\TestCase;
use Sto\Mediaoembed\Provider\ProviderEndpoints;

class ProviderEndpointsTest extends TestCase
{
    public function testGetEndpoints(): void
    {
        $endpoints = new ProviderEndpoints();
        self::assertTrue(is_array($endpoints->getEndpoints()));
    }
}
