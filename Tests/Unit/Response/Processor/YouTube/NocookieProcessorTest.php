<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Tests\Unit\Response\Processor\YouTube;

use Prophecy\PhpUnit\ProphecyTrait;
use Sto\Mediaoembed\Response\Processor\YouTube\NocookieProcessor;
use Sto\Mediaoembed\Response\VideoResponse;
use Sto\Mediaoembed\Service\UrlService;
use Sto\Mediaoembed\Tests\Unit\AbstractUnitTest;

class NocookieProcessorTest extends AbstractUnitTest
{
    use ProphecyTrait;

    public function testProcessResponse(): void
    {
        $videoHtml = '<iframe width="480" height="270" src="https://www.youtube.com/embed/P8bHMEh40JU?feature=oembed"'
            . ' allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"'
            . ' allowfullscreen=""></iframe>';
        $expectedHtml = str_replace('www.youtube.com', 'www.youtube-nocookie.com', $videoHtml);

        $videoProphecy = $this->prophesize(VideoResponse::class);
        $videoProphecy->getHtml()->shouldBeCalledOnce()->willReturn($videoHtml);
        $videoProphecy->setHtml($expectedHtml)->shouldBeCalledOnce();

        $processor = new NocookieProcessor(new UrlService());
        $processor->processResponse($videoProphecy->reveal());
    }
}
