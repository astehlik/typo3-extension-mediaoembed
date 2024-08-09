<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Tests\Unit\Content;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sto\Mediaoembed\Content\Configuration;
use Sto\Mediaoembed\Content\Settings;
use Sto\Mediaoembed\Domain\Model\Content;
use Sto\Mediaoembed\Service\AspectRatioCalculatorInterface;

class ConfigurationTest extends TestCase
{
    private AspectRatioCalculatorInterface|MockObject $aspectRatioCalculatorMock;

    private Content|MockObject $contentMock;

    private MockObject|Settings $settingsMock;

    protected function setUp(): void
    {
        $this->contentMock = $this->createMock(Content::class);
        $this->aspectRatioCalculatorMock = $this->createMock(AspectRatioCalculatorInterface::class);

        $this->settingsMock = $this->createMock(Settings::class);
    }

    public static function getMaxWidthHeightDataProvider(): iterable
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
        $this->aspectRatioCalculatorMock->expects(self::exactly(2))
            ->method('calculateAspectRatio')
            ->willReturnOnConsecutiveCalls(0.0, 1.5);

        $this->contentMock->expects(self::once())->method('getAspectRatio')->willReturn('12:1');
        $this->settingsMock->expects(self::once())->method('getAspectRatioFallback')->willReturn('12:2');

        self::assertSame(1.5, $this->getConfiguration()->getAspectRatio(0.0));
    }

    public function testGetAspectRatioUsesFallbackFromConstant(): void
    {
        $this->aspectRatioCalculatorMock->expects(self::exactly(3))
            ->method('calculateAspectRatio')
            ->willReturnOnConsecutiveCalls(0.0, 0.0, 1.24);

        $this->contentMock->expects(self::once())->method('getAspectRatio')->willReturn('12:1');

        $this->settingsMock->expects(self::once())->method('getAspectRatioFallback')->willReturn('12:2');

        self::assertSame(1.24, $this->getConfiguration()->getAspectRatio(0.0));
    }

    public function testGetAspectRatioUsesOverride(): void
    {
        $this->contentMock->expects(self::once())->method('getAspectRatio')->willReturn('12:1');
        $this->aspectRatioCalculatorMock->expects(self::once())
            ->method('calculateAspectRatio')
            ->with('12:1')
            ->willReturn(2.0);
        self::assertSame(2.0, $this->getConfiguration()->getAspectRatio(0.0));
    }

    public function testGetAspectRatioUsesResponse(): void
    {
        $this->contentMock->expects(self::once())->method('getAspectRatio')->willReturn('12:1');
        $this->aspectRatioCalculatorMock->expects(self::once())
            ->method('calculateAspectRatio')
            ->with('12:1')
            ->willReturn(0.0);
        self::assertSame(0.5, $this->getConfiguration()->getAspectRatio(0.5));
    }

    /**
     * @dataProvider getMaxWidthHeightDataProvider
     */
    public function testGetMaxheight(int $contentValue, int $settingsValue, int $expectedValue): void
    {
        $this->contentMock->method('getMaxHeight')->willReturn($contentValue);
        $this->settingsMock->method('getMaxHeight')->willReturn($settingsValue);

        self::assertSame($expectedValue, $this->getConfiguration()->getMaxheight());
    }

    /**
     * @dataProvider getMaxWidthHeightDataProvider
     */
    public function testGetMaxwidth(int $contentValue, int $settingsValue, int $expectedValue): void
    {
        $this->contentMock->method('getMaxWidth')->willReturn($contentValue);
        $this->settingsMock->method('getMaxWidth')->willReturn($settingsValue);

        self::assertSame($expectedValue, $this->getConfiguration()->getMaxwidth());
    }

    public function testGetMediaUrlReturnsUrlFromContent(): void
    {
        $this->contentMock->method('getUrl')->willReturn('http://my.test.url');

        self::assertSame('http://my.test.url', $this->getConfiguration()->getMediaUrl());
    }

    protected function getConfiguration(): Configuration
    {
        return new Configuration(
            $this->contentMock,
            $this->settingsMock,
            $this->aspectRatioCalculatorMock,
        );
    }
}
