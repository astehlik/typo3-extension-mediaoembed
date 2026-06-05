<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Tests\Unit\Domain\Model;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Sto\Mediaoembed\Domain\Model\Provider;
use Sto\Mediaoembed\Domain\Model\ProviderRequestHandlerConfig;

#[CoversClass(Provider::class)]
class ProviderTest extends TestCase
{
    public function testGetEndpoint(): void
    {
        $this->assertSame('https://the.provider.endpoint.url', $this->createProvider()->getEndpoint());
    }

    public function testGetName(): void
    {
        $this->assertSame('the_provider_name', $this->createProvider()->getName());
    }

    public function testGetRequestHandlerClass(): void
    {
        $provider = $this->createProvider(new ProviderRequestHandlerConfig('The\\Request\\Handler\\Class', []));
        $this->assertSame('The\\Request\\Handler\\Class', $provider->getRequestHandlerClass());
    }

    public function testGetRequestHandlerSettings(): void
    {
        $settings = ['this' => 'setting'];
        $provider = $this->createProvider(new ProviderRequestHandlerConfig('The\\Request\\Handler\\Class', $settings));
        $this->assertSame($settings, $provider->getRequestHandlerSettings());
    }

    public function testGetUrlSchemes(): void
    {
        $this->assertSame(['https://the.url.scheme'], $this->createProvider()->getUrlSchemes());
    }

    public function testHasRegexUrlSchemes(): void
    {
        $this->assertTrue($this->createProvider()->hasRegexUrlSchemes());
    }

    public function testShouldDirectLinkBeDisplayed(): void
    {
        $this->assertTrue($this->createProvider()->shouldDirectLinkBeDisplayed());
    }

    private function createProvider(?ProviderRequestHandlerConfig $requestHandlerConfig = null): Provider
    {
        return new Provider(
            'the_provider_name',
            'https://the.provider.endpoint.url',
            ['https://the.url.scheme'],
            true,
            requestHandlerConfig: $requestHandlerConfig,
        );
    }
}
