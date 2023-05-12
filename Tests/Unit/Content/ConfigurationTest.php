<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Tests\Unit\Content;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Sto\Mediaoembed\Content\Configuration;
use Sto\Mediaoembed\Content\Settings;
use Sto\Mediaoembed\Domain\Model\Content;
use Sto\Mediaoembed\Service\AspectRatioCalculatorInterface;

class ConfigurationTest extends TestCase
{
    use ProphecyTrait;

    private $aspectRatioCalculatorProphecy;

    private $settingsProphecy;

    private $contentProphecy;

    protected function setUp(): void
    {
        $this->contentProphecy = $this->prophesize(Content::class);
        $this->aspectRatioCalculatorProphecy = $this->prophesize(AspectRatioCalculatorInterface::class);

        $this->settingsProphecy = $this->prophesize(Settings::class);
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

    public function testGetAspectRatioUsesFallbackFromConfig(): void
    {
        $this->contentProphecy->getAspectRatio()->shouldBeCalledOnce()->willReturn('12:1');
        $this->aspectRatioCalculatorProphecy->calculateAspectRatio('12:1')->shouldBeCalledOnce()->willReturn(0);

        $this->settingsProphecy->getAspectRatioFallback()->shouldBeCalledOnce()->willReturn('12:2');
        $this->aspectRatioCalculatorProphecy->calculateAspectRatio('12:2')->shouldBeCalledOnce()->willReturn(1.5);

        self::assertSame(1.5, $this->getConfiguration()->getAspectRatio(0.0));
    }

    public function testGetAspectRatioUsesFallbackFromConstant(): void
    {
        $this->contentProphecy->getAspectRatio()->shouldBeCalledOnce()->willReturn('12:1');
        $this->aspectRatioCalculatorProphecy->calculateAspectRatio('12:1')->shouldBeCalledOnce()->willReturn(0);

        $this->settingsProphecy->getAspectRatioFallback()->shouldBeCalledOnce()->willReturn('12:2');
        $this->aspectRatioCalculatorProphecy->calculateAspectRatio('12:2')->shouldBeCalledOnce()->willReturn(0);

        $this->aspectRatioCalculatorProphecy->calculateAspectRatio('16:9')->shouldBeCalledOnce()->willReturn(1.24);

        self::assertSame(1.24, $this->getConfiguration()->getAspectRatio(0.0));
    }

    public function testGetAspectRatioUsesOverride(): void
    {
        $this->contentProphecy->getAspectRatio()->shouldBeCalledOnce()->willReturn('12:1');
        $this->aspectRatioCalculatorProphecy->calculateAspectRatio('12:1')->shouldBeCalledOnce()->willReturn(2);
        self::assertSame(2.0, $this->getConfiguration()->getAspectRatio(0.0));
    }

    public function testGetAspectRatioUsesResponse(): void
    {
        $this->contentProphecy->getAspectRatio()->shouldBeCalledOnce()->willReturn('12:1');
        $this->aspectRatioCalculatorProphecy->calculateAspectRatio('12:1')->shouldBeCalledOnce()->willReturn(0);
        self::assertSame(0.5, $this->getConfiguration()->getAspectRatio(0.5));
    }

    /**
     * @dataProvider getMaxWidthHeightDataProvider
     */
    public function testGetMaxheight(int $contentValue, int $settingsValue, int $expectedValue): void
    {
        $this->contentProphecy->getMaxHeight()->willReturn($contentValue);
        $this->settingsProphecy->getMaxHeight()->willReturn($settingsValue);

        self::assertSame($expectedValue, $this->getConfiguration()->getMaxheight());
    }

    /**
     * @dataProvider getMaxWidthHeightDataProvider
     */
    public function testGetMaxwidth(int $contentValue, int $settingsValue, int $expectedValue): void
    {
        $this->contentProphecy->getMaxWidth()->willReturn($contentValue);
        $this->settingsProphecy->getMaxWidth()->willReturn($settingsValue);

        self::assertSame($expectedValue, $this->getConfiguration()->getMaxwidth());
    }

    public function testGetMediaUrlReturnsUrlFromContent(): void
    {
        $this->contentProphecy->getUrl()->willReturn('http://my.test.url');

        self::assertSame('http://my.test.url', $this->getConfiguration()->getMediaUrl());
    }

    protected function getConfiguration(): Configuration
    {
        return new Configuration(
            $this->contentProphecy->reveal(),
            $this->settingsProphecy->reveal(),
            $this->aspectRatioCalculatorProphecy->reveal()
        );
    }
}
