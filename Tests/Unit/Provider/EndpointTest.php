<?php

namespace Sto\Mediaoembed\Tests\Unit\Provider;

use PHPUnit\Framework\TestCase;
use Sto\Mediaoembed\Provider\Endpoint;

class EndpointTest extends TestCase
{
    /**
     * @var Endpoint
     */
    private $endpoint;

    public function setUp()
    {
        $this->endpoint = new Endpoint('endpoint_name', 'https://my.endpoint.url', true);
    }

    public function testAddUrlScheme()
    {
        $this->endpoint->addUrlScheme('https://my.url.scheme');
        $this->assertEquals(['https://my.url.scheme'], $this->endpoint->getUrlSchemes());
    }

    public function testGetName()
    {
        $this->assertEquals('endpoint_name', $this->endpoint->getName());
    }

    public function testGetUrl()
    {
        $this->assertEquals('https://my.endpoint.url', $this->endpoint->getUrl());
    }

    public function testGetUrlConfigKeyForRegexes()
    {
        $this->assertEquals('urlRegexes', $this->endpoint->getUrlConfigKey());
    }

    public function testGetUrlConfigKeyForSchemes()
    {
        $endpoint = new Endpoint('test', 'https://test.url', false);
        $this->assertEquals('urlSchemes', $endpoint->getUrlConfigKey());
    }

    public function testIsRegex()
    {
        $this->assertTrue($this->endpoint->isRegex());
    }
}
