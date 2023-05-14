<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Tests\Unit\Provider;

use PHPUnit\Framework\TestCase;
use Sto\Mediaoembed\Provider\Endpoint;

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
        self::assertSame(['https://my.url.scheme'], $this->endpoint->getUrlSchemes());
    }

    public function testGetName(): void
    {
        self::assertSame('endpoint_name', $this->endpoint->getName());
    }

    public function testGetUrl(): void
    {
        self::assertSame('https://my.endpoint.url', $this->endpoint->getUrl());
    }

    public function testGetUrlConfigKeyForRegexes(): void
    {
        self::assertSame('urlRegexes', $this->endpoint->getUrlConfigKey());
    }

    public function testGetUrlConfigKeyForSchemes(): void
    {
        $endpoint = new Endpoint('test', 'https://test.url', false);
        self::assertSame('urlSchemes', $endpoint->getUrlConfigKey());
    }

    public function testIsRegex(): void
    {
        self::assertTrue($this->endpoint->isRegex());
    }
}
