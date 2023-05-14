<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Tests\Unit\Request;

use Sto\Mediaoembed\Domain\Model\Provider;
use Sto\Mediaoembed\Exception\NoMatchingProviderException;
use Sto\Mediaoembed\Request\ProviderResolver;
use Sto\Mediaoembed\Tests\Unit\AbstractUnitTestCase;

class ProviderResolverTest extends AbstractUnitTestCase
{
    /**
     * @var array|Provider[]
     */
    private $dummyProviders;

    /**
     * @var ProviderResolver
     */
    private $providerResolver;

    protected function setUp(): void
    {
        $firstProvider = new Provider('test1', 'https://endpoint', ['testurl'], false);
        $secondProvider = new Provider('test2', 'https://endpoint2', ['testurl'], false);

        $this->dummyProviders = [
            $firstProvider,
            $secondProvider,
        ];

        $this->providerResolver = new ProviderResolver($this->dummyProviders);
    }

    public function testResolverReturnsSecondMatchingProviderOnSecondCall(): void
    {
        $this->providerResolver->getNextMatchingProvider('testurl');
        $secondProvider = $this->providerResolver->getNextMatchingProvider('testurl');

        self::assertSame($this->dummyProviders[1], $secondProvider);
    }

    public function testResolverThrowsNoMatchingProviderException(): void
    {
        $this->providerResolver->getNextMatchingProvider('testurl');
        $this->providerResolver->getNextMatchingProvider('testurl');

        $this->expectException(NoMatchingProviderException::class);
        $this->providerResolver->getNextMatchingProvider('testurl');
    }
}
