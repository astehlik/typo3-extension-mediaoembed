<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Tests\Unit\Response\Processor\YouTube;

use Prophecy\PhpUnit\ProphecyTrait;
use Sto\Mediaoembed\Content\Configuration;
use Sto\Mediaoembed\Response\Processor\YouTube\PlayRelatedProcessor;
use Sto\Mediaoembed\Response\VideoResponse;
use Sto\Mediaoembed\Service\UrlService;
use Sto\Mediaoembed\Tests\Unit\AbstractUnitTest;

class PlayRelatedProcessorTest extends AbstractUnitTest
{
    use ProphecyTrait;

    public function processResponseModifesIframeUrlDataProvider(): array
    {
        return [
            [true],
            [false],
        ];
    }

    /**
     * @dataProvider processResponseModifesIframeUrlDataProvider
     */
    public function testProcessResponseModifesIframeUrl(bool $shouldPlayRelated): void
    {
        $videoHtmlTemplate = '<iframe width="480" height="270" src="%s"'
            . ' allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"'
            . ' allowfullscreen=""></iframe>';
        $originalUrl = 'https://www.youtube.com/embed/P8bHMEh40JU?start=14&feature=oembed';
        $modifiedUrl = 'https://www.youtube.com/embed/P8bHMEh40JU?start=14&amp;feature=oembed&amp;rel='
            . ($shouldPlayRelated ? '1' : '0');

        $videoHtml = sprintf($videoHtmlTemplate, $originalUrl);
        $expectedHtml = sprintf($videoHtmlTemplate, $modifiedUrl);

        $configurationProphecy = $this->prophesize(Configuration::class);
        $configurationProphecy->shouldPlayRelated()->shouldBeCalledOnce()->willReturn($shouldPlayRelated);

        $videoProphecy = $this->prophesize(VideoResponse::class);
        $videoProphecy->getHtml()->shouldBeCalledOnce()->willReturn($videoHtml);
        $videoProphecy->setHtml($expectedHtml)->shouldBeCalledOnce();
        $videoProphecy->getConfiguration()->shouldBeCalledOnce()->willReturn($configurationProphecy->reveal());

        $processor = new PlayRelatedProcessor(new UrlService());
        $processor->processResponse($videoProphecy->reveal());
    }
}
