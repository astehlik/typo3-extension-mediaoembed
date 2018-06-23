<?php

namespace Sto\Mediaoembed\Tests\Unit\Provider;

use PHPUnit\Framework\TestCase;
use Sto\Mediaoembed\Provider\ProviderEndpoints;

class ProviderEndpointsTest extends TestCase
{
    public function testGetEndpoints()
    {
        $endpoints = new ProviderEndpoints();
        $this->assertTrue(is_array($endpoints->getEndpoints()));
    }
}
