<?php
declare(strict_types=1);

namespace Sto\Mediaoembed\Tests\Unit\Request;

use Sto\Mediaoembed\Domain\Model\Provider;
use Sto\Mediaoembed\Domain\Repository\ProviderRepository;
use Sto\Mediaoembed\Request\ProviderResolver;
use Sto\Mediaoembed\Tests\Unit\AbstractUnitTest;

class ProviderResolverTest extends AbstractUnitTest
{
    /**
     * @var ProviderRepository|\Prophecy\Prophecy\ObjectProphecy
     */
    private $providerRepositoryProphecy;

    public function setUp()
    {
        parent::setUp();
        $this->providerRepositoryProphecy = $this->prophesize(ProviderRepository::class);
    }

    /**
     * @test
     * @throws \Sto\Mediaoembed\Exception\NoMatchingProviderException
     * @throws \Sto\Mediaoembed\Exception\InvalidConfigurationException
     */
    public function resolverReturnsSecondMatchingProviderOnSecondCall()
    {
        $firstProviderProphecy = $this->prophesize(Provider::class);
        $firstProviderProphecy->isResponsibleForUrl('testurl')->willReturn(true);

        $secondProviderProphecy = $this->prophesize(Provider::class);
        $secondProviderProphecy->isResponsibleForUrl('testurl')->willReturn(true);

        $providerArray = [
            $firstProviderProphecy->reveal(),
            $secondProviderProphecy->reveal(),
        ];

        $this->providerRepositoryProphecy->findAll()->willReturn($providerArray);

        $providerResolver = $this->getProviderResolver();
        $providerResolver->getNextMatchingProvider('testurl');
        $secondProvider = $providerResolver->getNextMatchingProvider('testurl');

        $this->assertSame($providerArray[1], $secondProvider);
    }

    /**
     * @return \Sto\Mediaoembed\Request\ProviderResolver
     * @throws \Sto\Mediaoembed\Exception\InvalidConfigurationException
     */
    private function getProviderResolver(): ProviderResolver
    {
        return new ProviderResolver($this->providerRepositoryProphecy->reveal());
    }
}
