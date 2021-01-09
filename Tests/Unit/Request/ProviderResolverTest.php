<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Tests\Unit\Request;

use Prophecy\Prophecy\ObjectProphecy;
use Sto\Mediaoembed\Domain\Model\Provider;
use Sto\Mediaoembed\Exception\NoMatchingProviderException;
use Sto\Mediaoembed\Request\ProviderResolver;
use Sto\Mediaoembed\Tests\Unit\AbstractUnitTest;

class ProviderResolverTest extends AbstractUnitTest
{
    /**
     * @var array|Provider[]
     */
    private $dummyProviders;

    /**
     * @var ProviderResolver
     */
    private $providerResolver;

    protected function setUp()
    {
        $firstProvider = new Provider('test1', 'https://endpoint', ['testurl'], false);
        $secondProvider = new Provider('test2', 'https://endpoint2', ['testurl'], false);

        $this->dummyProviders = [
            $firstProvider,
            $secondProvider,
        ];

        $this->providerResolver = new ProviderResolver($this->dummyProviders);
    }

    /**
     * @test
     */
    public function resolverReturnsSecondMatchingProviderOnSecondCall()
    {
        $this->providerResolver->getNextMatchingProvider('testurl');
        $secondProvider = $this->providerResolver->getNextMatchingProvider('testurl');

        $this->assertSame($this->dummyProviders[1], $secondProvider);
    }

    /**
     * @test
     */
    public function resolverThrowsNoMatchingProviderException()
    {
        $this->providerResolver->getNextMatchingProvider('testurl');
        $this->providerResolver->getNextMatchingProvider('testurl');

        $this->expectException(NoMatchingProviderException::class);
        $this->providerResolver->getNextMatchingProvider('testurl');
    }

    /**
     * @param string $urlScheme
     * @return \Prophecy\Prophecy\ObjectProphecy|\Sto\Mediaoembed\Domain\Model\Provider
     */
    private function getProviderProphecy(string $urlScheme): ObjectProphecy
    {
        $firstProviderProphecy = $this->prophesize(Provider::class);
        $firstProviderProphecy->getUrlSchemes()->willReturn([$urlScheme]);
        $firstProviderProphecy->hasRegexUrlSchemes()->willReturn(false);
        return $firstProviderProphecy;
    }
}
