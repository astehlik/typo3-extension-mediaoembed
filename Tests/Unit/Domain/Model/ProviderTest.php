<?php

namespace Sto\Mediaoembed\Tests\Unit\Domain\Model;

use PHPUnit\Framework\TestCase;
use Sto\Mediaoembed\Domain\Model\Provider;

class ProviderTest extends TestCase
{
    /**
     * @var Provider
     */
    private $provider;

    protected function setUp()
    {
        $this->provider = new Provider(
            'the_provider_name',
            'http://the.provider.endpoint.url',
            ['https://the.url.scheme'],
            true
        );
    }

    public function testGetEndpoint()
    {
        $this->assertEquals('http://the.provider.endpoint.url', $this->provider->getEndpoint());
    }

    public function testGetName()
    {
        $this->assertEquals('the_provider_name', $this->provider->getName());
    }

    public function testGetUrlSchemes()
    {
        $this->assertEquals(['https://the.url.scheme'], $this->provider->getUrlSchemes());
    }

    public function testHasRegexUrlSchemes()
    {
        $this->assertTrue($this->provider->hasRegexUrlSchemes());
    }
}
