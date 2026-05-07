<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Tests\Unit\Domain\Model;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Sto\Mediaoembed\Domain\Model\Provider;

#[CoversClass(Provider::class)]
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
        $this->assertSame('https://the.provider.endpoint.url', $this->provider->getEndpoint());
    }

    public function testGetName(): void
    {
        $this->assertSame('the_provider_name', $this->provider->getName());
    }

    public function testGetRequestHandlerClass(): void
    {
        $this->provider->withRequestHandler('The\\Request\\Handler\\Class', []);
        $this->assertSame('The\\Request\\Handler\\Class', $this->provider->getRequestHandlerClass());
    }

    public function testGetRequestHandlerSettings(): void
    {
        $settings = ['this' => 'setting'];
        $this->provider->withRequestHandler('The\\Request\\Handler\\Class', $settings);
        $this->assertSame($settings, $this->provider->getRequestHandlerSettings());
    }

    public function testGetUrlSchemes(): void
    {
        $this->assertSame(['https://the.url.scheme'], $this->provider->getUrlSchemes());
    }

    public function testHasRegexUrlSchemes(): void
    {
        $this->assertTrue($this->provider->hasRegexUrlSchemes());
    }

    public function testShouldDirectLinkBeDisplayed(): void
    {
        $this->assertTrue($this->provider->shouldDirectLinkBeDisplayed());
    }
}
