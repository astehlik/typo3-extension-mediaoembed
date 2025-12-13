<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Tests\Unit\Response\Processor\YouTube;

use PHPUnit\Framework\Attributes\DataProvider;
use Sto\Mediaoembed\Content\Configuration;
use Sto\Mediaoembed\Response\Processor\YouTube\PlayRelatedProcessor;
use Sto\Mediaoembed\Response\VideoResponse;
use Sto\Mediaoembed\Service\UrlService;
use Sto\Mediaoembed\Tests\Unit\AbstractUnitTestCase;

class PlayRelatedProcessorTest extends AbstractUnitTestCase
{
    #[DataProvider('provideProcessResponseModifesIframeUrlCases')]
    public function testProcessResponseModifesIframeUrl(bool $shouldPlayRelated): void
    {
        /** @noinspection HtmlUnknownTarget */
        $videoHtmlTemplate = '<iframe width="480" height="270" src="%s"'
            . ' allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"'
            . ' allowfullscreen=""></iframe>';
        $originalUrl = 'https://www.youtube.com/embed/P8bHMEh40JU?start=14&feature=oembed';
        $modifiedUrl = 'https://www.youtube.com/embed/P8bHMEh40JU?start=14&amp;feature=oembed&amp;rel='
            . ($shouldPlayRelated ? '1' : '0');

        $videoHtml = sprintf($videoHtmlTemplate, $originalUrl);
        $expectedHtml = sprintf($videoHtmlTemplate, $modifiedUrl);

        $configurationMock = $this->createMock(Configuration::class);
        $configurationMock->expects($this->once())->method('shouldPlayRelated')->willReturn($shouldPlayRelated);

        $videoMock = $this->createMock(VideoResponse::class);
        $videoMock->expects($this->once())->method('getHtml')->willReturn($videoHtml);
        $videoMock->expects($this->once())->method('setHtml')->with($expectedHtml);
        $videoMock->expects($this->once())->method('getConfiguration')->willReturn($configurationMock);

        $processor = new PlayRelatedProcessor(new UrlService());
        $processor->processResponse($videoMock);
    }

    public static function provideProcessResponseModifesIframeUrlCases(): iterable
    {
        return [
            [true],
            [false],
        ];
    }
}
