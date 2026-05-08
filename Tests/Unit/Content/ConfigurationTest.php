<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Tests\Unit\Content;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sto\Mediaoembed\Content\Configuration;
use Sto\Mediaoembed\Content\Settings;
use Sto\Mediaoembed\Domain\Model\Content;
use Sto\Mediaoembed\Service\AspectRatioCalculatorInterface;

#[CoversClass(Configuration::class)]
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

    public function testGetAspectRatioUsesFallbackFromConfig(): void
    {
        $this->aspectRatioCalculatorMock->expects($this->exactly(2))
            ->method('calculateAspectRatio')
            ->willReturnOnConsecutiveCalls(0.0, 1.5);

        $this->contentMock->expects($this->once())->method('getAspectRatio')->willReturn('12:1');
        $this->settingsMock->expects($this->once())->method('getAspectRatioFallback')->willReturn('12:2');

        $this->assertSame(1.5, $this->getConfiguration()->getAspectRatio(0.0));
    }

    public function testGetAspectRatioUsesFallbackFromConstant(): void
    {
        $this->aspectRatioCalculatorMock->expects($this->exactly(3))
            ->method('calculateAspectRatio')
            ->willReturnOnConsecutiveCalls(0.0, 0.0, 1.24);

        $this->contentMock->expects($this->once())->method('getAspectRatio')->willReturn('12:1');

        $this->settingsMock->expects($this->once())->method('getAspectRatioFallback')->willReturn('12:2');

        $this->assertSame(1.24, $this->getConfiguration()->getAspectRatio(0.0));
    }

    public function testGetAspectRatioUsesOverride(): void
    {
        $this->contentMock->expects($this->once())->method('getAspectRatio')->willReturn('12:1');
        $this->aspectRatioCalculatorMock->expects($this->once())
            ->method('calculateAspectRatio')
            ->with('12:1')
            ->willReturn(2.0);
        $this->assertSame(2.0, $this->getConfiguration()->getAspectRatio(0.0));
    }

    public function testGetAspectRatioUsesResponse(): void
    {
        $this->contentMock->expects($this->once())->method('getAspectRatio')->willReturn('12:1');
        $this->aspectRatioCalculatorMock->expects($this->once())
            ->method('calculateAspectRatio')
            ->with('12:1')
            ->willReturn(0.0);
        $this->assertSame(0.5, $this->getConfiguration()->getAspectRatio(0.5));
    }

    public function testGetContentUidReturnsUidFromContent(): void
    {
        $this->contentMock->method('getUid')->willReturn(123);

        $this->assertSame(123, $this->getConfiguration()->getContentUid());
    }

    public function testGetEmbedResponsiveClassReturnsDefault(): void
    {
        $this->settingsMock->method('getEmbedResponsiveClass')->willReturn('');
        $this->assertSame('tx-mediaoembed-embed ratio', $this->getConfiguration()->getEmbedResponsiveClass());
    }

    public function testGetEmbedResponsiveClassReturnsValueFromSettings(): void
    {
        $this->settingsMock->method('getEmbedResponsiveClass')->willReturn('my-class');
        $this->assertSame('my-class', $this->getConfiguration()->getEmbedResponsiveClass());
    }

    public function testGetHttpClientClassReturnsValueFromSettings(): void
    {
        $this->settingsMock->method('getHttpClientClass')->willReturn('MyClient');
        $this->assertSame('MyClient', $this->getConfiguration()->getHttpClientClass());
    }

    #[DataProvider('getMaxWidthHeightDataProvider')]
    public function testGetMaxheight(int $contentValue, int $settingsValue, int $expectedValue): void
    {
        $this->contentMock->method('getMaxHeight')->willReturn($contentValue);
        $this->settingsMock->method('getMaxHeight')->willReturn($settingsValue);

        $this->assertSame($expectedValue, $this->getConfiguration()->getMaxheight());
    }

    #[DataProvider('getMaxWidthHeightDataProvider')]
    public function testGetMaxwidth(int $contentValue, int $settingsValue, int $expectedValue): void
    {
        $this->contentMock->method('getMaxWidth')->willReturn($contentValue);
        $this->settingsMock->method('getMaxWidth')->willReturn($settingsValue);

        $this->assertSame($expectedValue, $this->getConfiguration()->getMaxwidth());
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

    public function testGetMediaUrlReturnsUrlFromContent(): void
    {
        $this->contentMock->method('getUrl')->willReturn('http://my.test.url');

        $this->assertSame('http://my.test.url', $this->getConfiguration()->getMediaUrl());
    }

    public function testGetPhotoDownloadFolderIdentifierReturnsValueFromSettings(): void
    {
        $this->settingsMock->method('getPhotoDownloadFolderIdentifier')->willReturn('folder');
        $this->assertSame('folder', $this->getConfiguration()->getPhotoDownloadFolderIdentifier());
    }

    public function testGetPhotoDownloadStorageUidReturnsValueFromSettings(): void
    {
        $this->settingsMock->method('getPhotoDownloadStorageUid')->willReturn(5);
        $this->assertSame(5, $this->getConfiguration()->getPhotoDownloadStorageUid());
    }

    public function testGetProcessorsForHtmlReturnsValueFromSettings(): void
    {
        $this->settingsMock->method('getProcessorsForHtml')->willReturn(['Processor']);
        $this->assertSame(['Processor'], $this->getConfiguration()->getProcessorsForHtml());
    }

    public function testIsConsentEnabledReturnsValueFromSettings(): void
    {
        $this->settingsMock->method('isConsentEnabled')->willReturn(true);
        $this->assertTrue($this->getConfiguration()->isConsentEnabled());
    }

    public function testIsConsentPreviewEnabledDelegatesToSettings(): void
    {
        $this->settingsMock->method('isConsentPreviewEnabled')->willReturn(true);

        $this->assertTrue($this->getConfiguration()->isConsentPreviewEnabled());
    }

    public function testIsConsentPreviewEnabledReturnsFalseWhenSettingsReturnsFalse(): void
    {
        $this->settingsMock->method('isConsentPreviewEnabled')->willReturn(false);

        $this->assertFalse($this->getConfiguration()->isConsentPreviewEnabled());
    }

    public function testIsPhotoDownloadEnabledReturnsValueFromSettings(): void
    {
        $this->settingsMock->method('isPhotoDownloadEnabled')->willReturn(true);
        $this->assertTrue($this->getConfiguration()->isPhotoDownloadEnabled());
    }

    public function testShouldPlayRelatedDelegatesToContent(): void
    {
        $this->contentMock->method('shouldPlayRelated')->willReturn(true);
        $this->assertTrue($this->getConfiguration()->shouldPlayRelated());
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
