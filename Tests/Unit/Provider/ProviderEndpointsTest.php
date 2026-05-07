<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Tests\Unit\Provider;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Sto\Mediaoembed\Provider\ProviderEndpoints;

#[CoversClass(ProviderEndpoints::class)]
class ProviderEndpointsTest extends TestCase
{
    public function testGetEndpoints(): void
    {
        $endpoints = new ProviderEndpoints();
        $this->assertCount(39, $endpoints->getEndpoints());
    }
}
