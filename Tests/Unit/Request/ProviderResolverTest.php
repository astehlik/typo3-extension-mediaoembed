<?php
declare(strict_types=1);

namespace Sto\Mediaoembed\Tests\Unit\Request;

use Prophecy\Prophecy\ObjectProphecy;
use Sto\Mediaoembed\Domain\Model\Provider;
use Sto\Mediaoembed\Request\ProviderResolver;
use Sto\Mediaoembed\Tests\Unit\AbstractUnitTest;

class ProviderResolverTest extends AbstractUnitTest
{
    /**
     * @test
     */
    public function resolverReturnsSecondMatchingProviderOnSecondCall()
    {
        $firstProviderProphecy = $this->getProviderProphecy('nomatch');
        $secondProviderProphecy = $this->getProviderProphecy('testurl');

        $providerList = [
            $firstProviderProphecy->reveal(),
            $secondProviderProphecy->reveal(),
        ];

        $providerResolver = new ProviderResolver($providerList);
        $providerResolver->getNextMatchingProvider('testurl');
        $secondProvider = $providerResolver->getNextMatchingProvider('testurl');

        $this->assertSame($providerList[1], $secondProvider);
    }

    /**
     * @param string $urlScheme
     * @return \Prophecy\Prophecy\ObjectProphecy|\Sto\Mediaoembed\Domain\Model\Provider
     */
    protected function getProviderProphecy(string $urlScheme): ObjectProphecy
    {
        $firstProviderProphecy = $this->prophesize(Provider::class);
        $firstProviderProphecy->getUrlSchemes()->willReturn([$urlScheme]);
        $firstProviderProphecy->hasRegexUrlSchemes()->willReturn(false);
        return $firstProviderProphecy;
    }
}
