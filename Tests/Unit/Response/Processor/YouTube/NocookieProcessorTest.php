<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Tests\Unit\Response\Processor\YouTube;

use Sto\Mediaoembed\Response\Processor\YouTube\NocookieProcessor;
use Sto\Mediaoembed\Response\VideoResponse;
use Sto\Mediaoembed\Service\UrlService;
use Sto\Mediaoembed\Tests\Unit\AbstractUnitTestCase;

class NocookieProcessorTest extends AbstractUnitTestCase
{
    public function testProcessResponse(): void
    {
        $videoHtml = '<iframe width="480" height="270" src="https://www.youtube.com/embed/P8bHMEh40JU?feature=oembed"'
            . ' allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"'
            . ' allowfullscreen=""></iframe>';
        $expectedHtml = str_replace('www.youtube.com', 'www.youtube-nocookie.com', $videoHtml);

        $videoMock = $this->createMock(VideoResponse::class);
        $videoMock->expects(self::once())->method('getHtml')->willReturn($videoHtml);
        $videoMock->expects(self::once())->method('setHtml')->with($expectedHtml);

        $processor = new NocookieProcessor(new UrlService());
        $processor->processResponse($videoMock);
    }
}
