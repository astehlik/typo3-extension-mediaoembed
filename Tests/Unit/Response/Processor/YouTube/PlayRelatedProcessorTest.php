<?php

namespace Sto\Mediaoembed\Tests\Unit\Response\Processor\YouTube;

use Sto\Mediaoembed\Content\Configuration;
use Sto\Mediaoembed\Response\Processor\YouTube\PlayRelatedProcessor;
use Sto\Mediaoembed\Response\VideoResponse;
use Sto\Mediaoembed\Tests\Unit\AbstractUnitTest;

class PlayRelatedProcessorTest extends AbstractUnitTest
{
    /**
     * @param bool $shouldPlayRelated
     *
     * @test
     * @dataProvider processResponseModifesIframeUrlDataProvider
     */
    public function processResponseModifesIframeUrl(bool $shouldPlayRelated)
    {
        $videoHtmlTemplate = '<iframe width="480" height="270" src="%s"'
            . ' allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"'
            . ' allowfullscreen=""></iframe>';
        $originalUrl = 'https://www.youtube.com/embed/P8bHMEh40JU?feature=oembed';
        $modifiedUrl = 'https://www.youtube.com/embed/P8bHMEh40JU?feature=oembed&amp;rel='
            . ($shouldPlayRelated ? '1' : '0');

        $videoHtml = sprintf($videoHtmlTemplate, $originalUrl);
        $expectedHtml = sprintf($videoHtmlTemplate, $modifiedUrl);

        $configurationProphecy = $this->prophesize(Configuration::class);
        $configurationProphecy->shouldPlayRelated()->shouldBeCalledOnce()->willReturn($shouldPlayRelated);

        $videoProphecy = $this->prophesize(VideoResponse::class);
        $videoProphecy->getHtml()->shouldBeCalledOnce()->willReturn($videoHtml);
        $videoProphecy->setHtml($expectedHtml)->shouldBeCalledOnce();

        $processor = new PlayRelatedProcessor();
        $processor->injectConfiguration($configurationProphecy->reveal());
        $processor->processResponse($videoProphecy->reveal());
    }

    public function processResponseModifesIframeUrlDataProvider(): array
    {
        return [
            [true],
            [false],
        ];
    }
}
