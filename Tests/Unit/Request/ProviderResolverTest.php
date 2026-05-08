<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Tests\Unit\Request;

use PHPUnit\Framework\Attributes\CoversClass;
use Sto\Mediaoembed\Domain\Model\Provider;
use Sto\Mediaoembed\Exception\NoMatchingProviderException;
use Sto\Mediaoembed\Request\ProviderResolver;
use Sto\Mediaoembed\Tests\Unit\AbstractUnitTestCase;

#[CoversClass(ProviderResolver::class)]
class ProviderResolverTest extends AbstractUnitTestCase
{
    /**
     * @var array<Provider>
     */
    private array $dummyProviders;

    private ProviderResolver $providerResolver;

    protected function setUp(): void
    {
        $this->dummyProviders = [
            new Provider('test1', 'https://endpoint', ['testurl'], false),
            new Provider('test2', 'https://endpoint2', ['testurl'], false),
            new Provider('wildcard', 'https://endpoint_wildcard', ['wildcard*test'], false),
            new Provider('http', 'https://endpoint_http', ['http://url_with_http'], false),
            new Provider('regex', 'https://endpoint_regex', ['#https?://my\\.provider\\.tld/playlist.*#i'], true),
        ];

        $this->providerResolver = new ProviderResolver($this->dummyProviders);
    }

    public function testResolverMatchesHttpsUrlIfHttpUrlIsConfigured(): void
    {
        $provider = $this->providerResolver->getNextMatchingProvider('https://url_with_http');

        $this->assertSame($this->dummyProviders[3], $provider);
    }

    public function testResolverMatchesRegexUrl(): void
    {
        $provider = $this->providerResolver->getNextMatchingProvider('https://my.provider.tld/playlist/blabla');

        $this->assertSame($this->dummyProviders[4], $provider);
    }

    public function testResolverMatchesUrlWithWildcard(): void
    {
        $provider = $this->providerResolver->getNextMatchingProvider('wildcard_fill_test');

        $this->assertSame($this->dummyProviders[2], $provider);
    }

    public function testResolverReturnsSecondMatchingProviderOnSecondCall(): void
    {
        $this->providerResolver->getNextMatchingProvider('testurl');
        $secondProvider = $this->providerResolver->getNextMatchingProvider('testurl');

        $this->assertSame($this->dummyProviders[1], $secondProvider);
    }

    public function testResolverThrowsNoMatchingProviderException(): void
    {
        $this->providerResolver->getNextMatchingProvider('testurl');
        $this->providerResolver->getNextMatchingProvider('testurl');

        $this->expectException(NoMatchingProviderException::class);
        $this->providerResolver->getNextMatchingProvider('testurl');
    }
}
