<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Tests\Unit\Provider;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Sto\Mediaoembed\Provider\Endpoint;

#[CoversClass(Endpoint::class)]
class EndpointTest extends TestCase
{
    /**
     * @var Endpoint
     */
    private $endpoint;

    protected function setUp(): void
    {
        $this->endpoint = new Endpoint('endpoint_name', 'https://my.endpoint.url', true);
    }

    public function testAddUrlScheme(): void
    {
        $this->endpoint->addUrlScheme('https://my.url.scheme');
        $this->assertSame(['https://my.url.scheme'], $this->endpoint->getUrlSchemes());
    }

    public function testGetName(): void
    {
        $this->assertSame('endpoint_name', $this->endpoint->getName());
    }

    public function testGetUrl(): void
    {
        $this->assertSame('https://my.endpoint.url', $this->endpoint->getUrl());
    }

    public function testGetUrlConfigKeyForRegexes(): void
    {
        $this->assertSame('urlRegexes', $this->endpoint->getUrlConfigKey());
    }

    public function testGetUrlConfigKeyForSchemes(): void
    {
        $endpoint = new Endpoint('test', 'https://test.url', false);
        $this->assertSame('urlSchemes', $endpoint->getUrlConfigKey());
    }

    public function testIsRegex(): void
    {
        $this->assertTrue($this->endpoint->isRegex());
    }
}
