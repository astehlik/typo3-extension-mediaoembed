<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Tests\Unit\Provider;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Sto\Mediaoembed\Provider\Endpoint;
use Sto\Mediaoembed\Provider\EndpointCollector;
use Sto\Mediaoembed\Provider\ProviderEndpoints;
use Sto\Mediaoembed\Provider\ProviderUrls;

class EndpointCollectorTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var EndpointCollector
     */
    private $collector;

    private $providerEndpointsProphecy;

    private $providerUrlsProphecy;

    protected function setUp(): void
    {
        $this->providerEndpointsProphecy = $this->prophesize(ProviderEndpoints::class);
        $this->providerUrlsProphecy = $this->prophesize(ProviderUrls::class);

        $this->collector = new EndpointCollector(
            $this->providerEndpointsProphecy->reveal(),
            $this->providerUrlsProphecy->reveal()
        );
    }

    public function testCollectEndpointCollectsEndpointUrls(): void
    {
        $this->providerEndpointsProphecy->getEndpoints()->willReturn(
            ['https://some.existing.endpoint/' => 'name']
        );
        $this->providerUrlsProphecy->getUrls()->willReturn(
            [
                '#https?://testscheme2/.*#i' => [
                    'https://some.existing.endpoint/',
                    true,
                ],
                '#https?://testscheme1/.*#i' => [
                    'https://some.existing.endpoint/',
                    true,
                ],
            ]
        );

        $expectedEndpoint = new Endpoint('name', 'https://some.existing.endpoint/', true);
        $expectedEndpoint->addUrlScheme('#https?://testscheme2/.*#i');
        $expectedEndpoint->addUrlScheme('#https?://testscheme1/.*#i');

        $endpoints = $this->collector->collectEndpoints();
        $collectedEndpoint = $endpoints['name'];

        self::assertSame($expectedEndpoint->getName(), $collectedEndpoint->getName());
        self::assertSame($expectedEndpoint->getUrl(), $collectedEndpoint->getUrl());
        self::assertSame($expectedEndpoint->getUrlSchemes(), $collectedEndpoint->getUrlSchemes());
        self::assertSame($expectedEndpoint->getUrlConfigKey(), $collectedEndpoint->getUrlConfigKey());
    }

    public function testCollectEndpointOrdersByName(): void
    {
        $this->providerEndpointsProphecy->getEndpoints()->willReturn(
            [
                'https://some.existing.endpoint1/' => 'name2',
                'https://some.existing.endpoint2/' => 'name1',
            ]
        );
        $this->providerUrlsProphecy->getUrls()->willReturn(
            [
                '#https?://testscheme2/.*#i' => [
                    'https://some.existing.endpoint1/',
                    true,
                ],
                '#https?://testscheme1/.*#i' => [
                    'https://some.existing.endpoint2/',
                    true,
                ],
            ]
        );

        $endpoints = $this->collector->collectEndpoints();
        $expecteOrder = [
            'name1',
            'name2',
        ];
        self::assertSame($expecteOrder, array_keys($endpoints));
    }

    public function testCollectEndpointsThrowsExceptionForDuplicateProviderName(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Duplicate endpoint label name1');

        $this->providerEndpointsProphecy->getEndpoints()->willReturn(
            [
                'http://testurl.d' => 'name1',
                'http://testurl.de' => 'name1',
            ]
        );
        $this->providerUrlsProphecy->getUrls()->willReturn([]);

        $this->collector->collectEndpoints();
    }

    public function testCollectEndpointsThrowsExceptionIfEndpointLabelIsMissing(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('No label configured for endpoint URL https://some.existing.endpoint/');

        $this->providerEndpointsProphecy->getEndpoints()->willReturn(
            ['#https?://testscheme1/.*#i' => 'name1']
        );
        $this->providerUrlsProphecy->getUrls()->willReturn(
            [
                '#https?://testscheme1/.*#i' => [
                    'https://some.existing.endpoint/',
                    true,
                ],
                '#https?://testscheme2/.*#i' => [
                    'https://some.non.existing.endpoint/',
                    true,
                ],
            ]
        );

        $this->collector->collectEndpoints();
    }
}
