<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Tests\Unit\Response\Processor\YouTube;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use Sto\Mediaoembed\Response\GenericResponse;
use Sto\Mediaoembed\Response\Processor\Support\IframeManipulator;
use Sto\Mediaoembed\Response\Processor\YouTube\NocookieProcessor;
use Sto\Mediaoembed\Response\VideoResponse;
use Sto\Mediaoembed\Service\UrlService;
use Sto\Mediaoembed\Tests\Unit\AbstractUnitTestCase;

#[CoversClass(NocookieProcessor::class)]
class NocookieProcessorTest extends AbstractUnitTestCase
{
    private NocookieProcessor $processor;

    protected function setUp(): void
    {
        $this->processor = new NocookieProcessor(new IframeManipulator(new UrlService()), new UrlService());
    }

    public function testProcessResponse(): void
    {
        $videoHtml = '<iframe width="480" height="270" src="https://www.youtube.com/embed/P8bHMEh40JU?feature=oembed"'
            . ' allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"'
            . ' allowfullscreen=""></iframe>';
        $expectedHtml = str_replace('www.youtube.com', 'www.youtube-nocookie.com', $videoHtml);

        $videoMock = $this->createMock(VideoResponse::class);
        $videoMock->expects($this->once())->method('getHtml')->willReturn($videoHtml);
        $videoMock->expects($this->once())->method('setHtml')->with($expectedHtml);

        $this->processor->processResponse($videoMock);
    }

    public function testProcessResponseDoesNothingIfUrlIsAlreadyNocookie(): void
    {
        $videoHtml = '<iframe src="https://www.youtube-nocookie.com/embed/P8bHMEh40JU"></iframe>';

        $videoMock = $this->createMock(VideoResponse::class);
        $videoMock->method('getHtml')->willReturn($videoHtml);
        $videoMock->expects($this->never())->method('setHtml');

        $this->processor->processResponse($videoMock);
    }

    public function testProcessResponseDoesNothingIfUrlIsEmpty(): void
    {
        $videoHtml = '<iframe src=""></iframe>';

        $videoMock = $this->createMock(VideoResponse::class);
        $videoMock->method('getHtml')->willReturn($videoHtml);
        $videoMock->expects($this->never())->method('setHtml');

        $this->processor->processResponse($videoMock);
    }

    public function testProcessResponseThrowsExceptionForInvalidResponseType(): void
    {
        $responseMock = $this->createMock(GenericResponse::class);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('This processor only works with video responses!');

        $this->processor->processResponse($responseMock);
    }

    public function testProcessResponseThrowsExceptionIfNoIframe(): void
    {
        $videoHtml = '<div>not an iframe</div>';
        $videoMock = $this->createMock(VideoResponse::class);
        $videoMock->method('getHtml')->willReturn($videoHtml);

        $this->expectExceptionMessage('Expected HTML to be iframe but was: div');
        $this->processor->processResponse($videoMock);
    }
}
