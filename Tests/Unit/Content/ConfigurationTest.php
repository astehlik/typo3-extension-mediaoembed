<?php

namespace Sto\Mediaoembed\Tests\Unit\Content;

use PHPUnit\Framework\TestCase;
use Sto\Mediaoembed\Content\Configuration;
use Sto\Mediaoembed\Domain\Model\Content;
use Sto\Mediaoembed\Domain\Repository\ContentRepository;
use Sto\Mediaoembed\Service\AspectRatioCalculatorInterface;
use Sto\Mediaoembed\Service\ConfigurationService;

class ConfigurationTest extends TestCase
{
    private $aspectRatioCalculatorProphecy;

    private $configurationServiceProphecy;

    private $contentProphecy;

    private $contentRepositoryProphecy;

    public function setUp()
    {
        $this->contentProphecy = $this->prophesize(Content::class);
        $this->aspectRatioCalculatorProphecy = $this->prophesize(AspectRatioCalculatorInterface::class);
        $this->contentRepositoryProphecy = $this->prophesize(ContentRepository::class);
        $this->contentRepositoryProphecy->getCurrentContent()->willReturn($this->contentProphecy->reveal());

        $this->configurationServiceProphecy = $this->prophesize(ConfigurationService::class);
    }

    public function getMaxWidthHeightDataProvider(): array
    {
        return [
            'settings and content object zero returns zero' => [
                0,
                0,
                0,
            ],
            'settings zero, content object set uses content object' => [
                10,
                0,
                10,
            ],
            'settings set, content object zero uses settings' => [
                0,
                20,
                20,
            ],
            'settings set, content object set uses content object' => [
                30,
                20,
                30,
            ],
        ];
    }

    public function testGetAspectRatioUsesFallbackFromConfig()
    {
        $this->contentProphecy->getAspectRatio()->shouldBeCalledOnce()->willReturn('12:1');
        $this->aspectRatioCalculatorProphecy->calculateAspectRatio('12:1')->shouldBeCalledOnce()->willReturn(0);

        $this->configurationServiceProphecy->getAspectRatioFallback()->shouldBeCalledOnce()->willReturn('12:2');
        $this->aspectRatioCalculatorProphecy->calculateAspectRatio('12:2')->shouldBeCalledOnce()->willReturn(1.5);

        $this->assertEquals(1.5, $this->getConfiguration()->getAspectRatio(0.0));
    }

    public function testGetAspectRatioUsesFallbackFromConstant()
    {
        $this->contentProphecy->getAspectRatio()->shouldBeCalledOnce()->willReturn('12:1');
        $this->aspectRatioCalculatorProphecy->calculateAspectRatio('12:1')->shouldBeCalledOnce()->willReturn(0);

        $this->configurationServiceProphecy->getAspectRatioFallback()->shouldBeCalledOnce()->willReturn('12:2');
        $this->aspectRatioCalculatorProphecy->calculateAspectRatio('12:2')->shouldBeCalledOnce()->willReturn(0);

        $this->aspectRatioCalculatorProphecy->calculateAspectRatio('16:9')->shouldBeCalledOnce()->willReturn(1.24);

        $this->assertEquals(1.24, $this->getConfiguration()->getAspectRatio(0.0));
    }

    public function testGetAspectRatioUsesOverride()
    {
        $this->contentProphecy->getAspectRatio()->shouldBeCalledOnce()->willReturn('12:1');
        $this->aspectRatioCalculatorProphecy->calculateAspectRatio('12:1')->shouldBeCalledOnce()->willReturn(2);
        $this->assertEquals(2, $this->getConfiguration()->getAspectRatio(0.0));
    }

    public function testGetAspectRatioUsesResponse()
    {
        $this->contentProphecy->getAspectRatio()->shouldBeCalledOnce()->willReturn('12:1');
        $this->aspectRatioCalculatorProphecy->calculateAspectRatio('12:1')->shouldBeCalledOnce()->willReturn(0);
        $this->assertEquals(0.5, $this->getConfiguration()->getAspectRatio(0.5));
    }

    /**
     * @param int $contentValue
     * @param int $settingsValue
     * @param int $expectedValue
     * @dataProvider getMaxWidthHeightDataProvider
     */
    public function testGetMaxheight(int $contentValue, int $settingsValue, int $expectedValue)
    {
        $this->contentProphecy->getMaxHeight()->willReturn($contentValue);
        $this->configurationServiceProphecy->getMaxHeight()->willReturn($settingsValue);

        $this->assertEquals($expectedValue, $this->getConfiguration()->getMaxheight());
    }

    /**
     * @param int $contentValue
     * @param int $settingsValue
     * @param int $expectedValue
     * @dataProvider getMaxWidthHeightDataProvider
     */
    public function testGetMaxwidth(int $contentValue, int $settingsValue, int $expectedValue)
    {
        $this->contentProphecy->getMaxWidth()->willReturn($contentValue);
        $this->configurationServiceProphecy->getMaxWidth()->willReturn($settingsValue);

        $this->assertEquals($expectedValue, $this->getConfiguration()->getMaxwidth());
    }

    public function testGetMediaUrlReturnsUrlFromContent()
    {
        $this->contentProphecy->getUrl()->willReturn('http://my.test.url');

        $this->assertEquals('http://my.test.url', $this->getConfiguration()->getMediaUrl());
    }

    protected function getConfiguration(): Configuration
    {
        return new Configuration(
            $this->aspectRatioCalculatorProphecy->reveal(),
            $this->configurationServiceProphecy->reveal(),
            $this->contentRepositoryProphecy->reveal()
        );
    }
}
