<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Tests\Unit\Domain\Model;

use PHPUnit\Framework\TestCase;
use Sto\Mediaoembed\Domain\Model\Provider;

class ProviderTest extends TestCase
{
    /**
     * @var Provider
     */
    private $provider;

    protected function setUp(): void
    {
        $this->provider = new Provider(
            'the_provider_name',
            'https://the.provider.endpoint.url',
            ['https://the.url.scheme'],
            true,
        );
    }

    public function testGetEndpoint(): void
    {
        self::assertSame('https://the.provider.endpoint.url', $this->provider->getEndpoint());
    }

    public function testGetName(): void
    {
        self::assertSame('the_provider_name', $this->provider->getName());
    }

    public function testGetRequestHandlerClass(): void
    {
        $this->provider->withRequestHandler('The\\Request\\Handler\\Class', []);
        self::assertSame('The\\Request\\Handler\\Class', $this->provider->getRequestHandlerClass());
    }

    public function testGetRequestHandlerSettings(): void
    {
        $settings = ['this' => 'setting'];
        $this->provider->withRequestHandler('The\\Request\\Handler\\Class', $settings);
        self::assertSame($settings, $this->provider->getRequestHandlerSettings());
    }

    public function testGetUrlSchemes(): void
    {
        self::assertSame(['https://the.url.scheme'], $this->provider->getUrlSchemes());
    }

    public function testHasRegexUrlSchemes(): void
    {
        self::assertTrue($this->provider->hasRegexUrlSchemes());
    }

    public function testShouldDirectLinkBeDisplayed(): void
    {
        self::assertTrue($this->provider->shouldDirectLinkBeDisplayed());
    }
}
