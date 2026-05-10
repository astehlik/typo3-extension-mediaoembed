<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Tests\Unit\Content;

use PHPUnit\Framework\Attributes\CoversClass;
use Sto\Mediaoembed\Content\Settings;
use Sto\Mediaoembed\Tests\Unit\AbstractUnitTestCase;

#[CoversClass(Settings::class)]
final class SettingsTest extends AbstractUnitTestCase
{
    public function testGetAspectRatioFallbackReturnsConfiguredValue(): void
    {
        $settings = new Settings(['aspectRatioFallback' => '4:3']);
        $this->assertSame('4:3', $settings->getAspectRatioFallback());
    }

    public function testGetAspectRatioFallbackReturnsDefault(): void
    {
        $settings = new Settings([]);
        $this->assertSame('', $settings->getAspectRatioFallback());
    }

    public function testGetEmbedResponsiveClassReturnsConfiguredValue(): void
    {
        $settings = new Settings(['embedResponsiveClass' => 'custom-class']);
        $this->assertSame('custom-class', $settings->getEmbedResponsiveClass());
    }

    public function testGetEmbedResponsiveClassReturnsDefault(): void
    {
        $settings = new Settings([]);
        $this->assertSame('', $settings->getEmbedResponsiveClass());
    }

    public function testGetEmbedResponsiveStylePropertyReturnsConfiguredValue(): void
    {
        $settings = new Settings(['embedResponsiveStyleProperty' => 'padding-top']);
        $this->assertSame('padding-top', $settings->getEmbedResponsiveStyleProperty());
    }

    public function testGetEmbedResponsiveStylePropertyReturnsDefault(): void
    {
        $settings = new Settings([]);
        $this->assertSame('', $settings->getEmbedResponsiveStyleProperty());
    }

    public function testGetHttpClientClassReturnsConfiguredValue(): void
    {
        $settings = new Settings(['httpClient' => 'Custom\\HttpClient']);
        $this->assertSame('Custom\\HttpClient', $settings->getHttpClientClass());
    }

    public function testGetHttpClientClassReturnsDefault(): void
    {
        $settings = new Settings([]);
        $this->assertSame('', $settings->getHttpClientClass());
    }

    public function testGetMaxHeightReturnsConfiguredValue(): void
    {
        $settings = new Settings(['media' => ['maxheight' => '480']]);
        $this->assertSame(480, $settings->getMaxHeight());
    }

    public function testGetMaxHeightReturnsDefault(): void
    {
        $settings = new Settings([]);
        $this->assertSame(0, $settings->getMaxHeight());
    }

    public function testGetMaxHeightReturnsDefaultForEmptyMediaSettings(): void
    {
        $settings = new Settings(['media' => []]);
        $this->assertSame(0, $settings->getMaxHeight());
    }

    public function testGetMaxWidthReturnsConfiguredValue(): void
    {
        $settings = new Settings(['media' => ['maxwidth' => '800']]);
        $this->assertSame(800, $settings->getMaxWidth());
    }

    public function testGetMaxWidthReturnsDefault(): void
    {
        $settings = new Settings([]);
        $this->assertSame(0, $settings->getMaxWidth());
    }

    public function testGetMaxWidthReturnsDefaultForEmptyMediaSettings(): void
    {
        $settings = new Settings(['media' => []]);
        $this->assertSame(0, $settings->getMaxWidth());
    }

    public function testGetPhotoDownloadFolderIdentifierReturnsConfiguredValue(): void
    {
        $settings = new Settings([
            'downloadPhotoSettings' => ['folderIdentifier' => 'custom-folder'],
        ]);
        $this->assertSame('custom-folder', $settings->getPhotoDownloadFolderIdentifier());
    }

    public function testGetPhotoDownloadFolderIdentifierReturnsDefault(): void
    {
        $settings = new Settings([]);
        $this->assertSame('', $settings->getPhotoDownloadFolderIdentifier());
    }

    public function testGetPhotoDownloadStorageUidReturnsConfiguredValue(): void
    {
        $settings = new Settings([
            'downloadPhotoSettings' => ['storageUid' => 5],
        ]);
        $this->assertSame(5, $settings->getPhotoDownloadStorageUid());
    }

    public function testGetPhotoDownloadStorageUidReturnsDefault(): void
    {
        $settings = new Settings([]);
        $this->assertSame(0, $settings->getPhotoDownloadStorageUid());
    }

    public function testGetProcessorsForHtmlReturnsConfiguredValue(): void
    {
        $processors = [
            'processor1',
            'processor2',
        ];
        $settings = new Settings([
            'reponseProcessors' => ['html' => $processors],
        ]);
        $this->assertSame($processors, $settings->getProcessorsForHtml());
    }

    public function testGetProcessorsForHtmlReturnsDefault(): void
    {
        $settings = new Settings([]);
        $this->assertSame([], $settings->getProcessorsForHtml());
    }

    public function testIsConsentEnabledReturnsConfiguredValue(): void
    {
        $settings = new Settings(['consent' => ['enabled' => true]]);
        $this->assertTrue($settings->isConsentEnabled());
    }

    public function testIsConsentEnabledReturnsDefault(): void
    {
        $settings = new Settings([]);
        $this->assertFalse($settings->isConsentEnabled());
    }

    public function testIsConsentPreviewEnabledReturnsConfiguredValue(): void
    {
        $settings = new Settings(['consent' => ['showPreview' => true]]);
        $this->assertTrue($settings->isConsentPreviewEnabled());
    }

    public function testIsConsentPreviewEnabledReturnsDefault(): void
    {
        $settings = new Settings([]);
        $this->assertFalse($settings->isConsentPreviewEnabled());
    }

    public function testIsPhotoDownloadEnabledReturnsConfiguredValue(): void
    {
        $settings = new Settings(['downloadPhoto' => true]);
        $this->assertTrue($settings->isPhotoDownloadEnabled());
    }

    public function testIsPhotoDownloadEnabledReturnsDefault(): void
    {
        $settings = new Settings(['downloadPhoto' => false]);
        $this->assertFalse($settings->isPhotoDownloadEnabled());
    }
}
