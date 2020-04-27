<?php

namespace Sto\Mediaoembed\Tests\Unit\Response\Processor;

use DOMDocument;
use DOMElement;
use InvalidArgumentException;
use Sto\Mediaoembed\Exception\ProcessorException;
use Sto\Mediaoembed\Response\GenericResponse;
use Sto\Mediaoembed\Response\Processor\YoutubeNocookieProcessor;
use Sto\Mediaoembed\Response\VideoResponse;
use Sto\Mediaoembed\Tests\Unit\AbstractUnitTest;

class YoutubeNocookieProcessorTest extends AbstractUnitTest
{
    public function testProcessResponse()
    {
        $videoHtml = '<iframe width="480" height="270" src="https://www.youtube.com/embed/P8bHMEh40JU?feature=oembed"'
            . ' allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"'
            . ' allowfullscreen=""></iframe>';
        $expectedHtml = str_replace('www.youtube.com', 'www.youtube-nocookie.com', $videoHtml);

        $videoProphecy = $this->prophesize(VideoResponse::class);
        $videoProphecy->getHtml()->shouldBeCalledOnce()->willReturn($videoHtml);
        $videoProphecy->setHtml($expectedHtml)->shouldBeCalledOnce();

        $processor = new YoutubeNocookieProcessor();
        $processor->processResponse($videoProphecy->reveal());
    }
}
