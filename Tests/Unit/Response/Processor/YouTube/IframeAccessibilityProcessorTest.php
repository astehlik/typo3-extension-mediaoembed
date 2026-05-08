<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Tests\Unit\Response\Processor\YouTube;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use Sto\Mediaoembed\Response\HtmlAwareResponseInterface;
use Sto\Mediaoembed\Response\Processor\Support\IframeManipulator;
use Sto\Mediaoembed\Response\Processor\YouTube\IframeAccessibilityProcessor;
use Sto\Mediaoembed\Response\VideoResponse;
use Sto\Mediaoembed\Service\LocalizationService;
use Sto\Mediaoembed\Tests\Unit\AbstractUnitTestCase;

#[CoversClass(IframeAccessibilityProcessor::class)]
class IframeAccessibilityProcessorTest extends AbstractUnitTestCase
{
    public const PROVIDER_NAME = 'MyProvider';

    private MockObject|LocalizationService $localizationServiceMock;

    private IframeAccessibilityProcessor $processor;

    protected function setUp(): void
    {
        $this->localizationServiceMock = $this->createMock(LocalizationService::class);

        $this->processor = new IframeAccessibilityProcessor(new IframeManipulator(), $this->localizationServiceMock);
    }

    public function testProcessHtmlResponseAddsFallbackAriaLabelForEmptyTitle(): void
    {
        $responseMock = $this->createVideoResponseMock(
            '<iframe src="https://example.com"></iframe>',
            '',
        );

        $fallbackAriaLabel = 'Embedded video from YouTube: Fallback aria label';

        $this->localizationServiceMock->expects($this->once())
            ->method('translate')
            ->with('iframe_aria_label_fallback', [self::PROVIDER_NAME])
            ->willReturn($fallbackAriaLabel);

        $responseMock->expects($this->once())
            ->method('setHtml')
            ->with($this->stringContains(sprintf('aria-label="%s"', $fallbackAriaLabel)));

        $this->processor->processHtmlResponse($responseMock);
    }

    public function testProcessHtmlResponseDoesNothingIfHtmlDoesNotStartWithIframe(): void
    {
        $responseMock = $this->createVideoResponseMock('<div><iframe src="https://example.com"></iframe></div>');

        $this->expectHtmlIsNotModified($responseMock);

        $this->processor->processHtmlResponse($responseMock);
    }

    public function testProcessHtmlResponseDoesNothingIfNotIframe(): void
    {
        $responseMock = $this->createVideoResponseMock('<div>no iframe</div>');

        $this->expectHtmlIsNotModified($responseMock);

        $this->processor->processHtmlResponse($responseMock);
    }

    public function testProcessHtmlResponseDoesNotOverrideExistingAriaLabel(): void
    {
        $responseMock = $this->createVideoResponseMock(
            '<iframe src="https://example.com" aria-label="Custom label"></iframe>',
            'Test Video Title',
        );

        $this->expectHtmlIsNotModified($responseMock);

        $this->processor->processHtmlResponse($responseMock);
    }

    public function testProcessHtmlResponseEscapesHtmlSpecialCharactersInAriaLabel(): void
    {
        $title = 'Video with <special> & "characters" and different \'quotes\'';

        $responseMock = $this->createVideoResponseMock(
            '<iframe src="https://example.com"></iframe>',
            $title,
        );

        $expectedTranslation = $this->initializeTranslationServiceMockForAriaLabel($title);
        $expectedEscaped = htmlspecialchars($expectedTranslation, ENT_COMPAT);

        $responseMock->expects($this->once())
            ->method('setHtml')
            ->with($this->stringContains(sprintf('aria-label="%s"', $expectedEscaped)));

        $this->processor->processHtmlResponse($responseMock);
    }

    public function testProcessHtmlResponseThrowsExceptionIfNotGenericResponse(): void
    {
        $html = '<iframe src="https://example.com"></iframe>';
        $responseMock = $this->createMock(HtmlAwareResponseInterface::class);
        $responseMock->method('getHtml')->willReturn($html);

        $this->expectException(InvalidArgumentException::class);
        $this->processor->processHtmlResponse($responseMock);
    }

    private function createVideoResponseMock(
        string $html,
        string $title = '',
    ): MockObject|VideoResponse {
        $responseMock = $this->createMock(VideoResponse::class);
        $responseMock->method('getHtml')->willReturn($html);
        $responseMock->method('getTitle')->willReturn($title);
        $responseMock->method('getProviderName')->willReturn(self::PROVIDER_NAME);

        return $responseMock;
    }

    private function expectHtmlIsNotModified(MockObject|VideoResponse $responseMock): void
    {
        $this->localizationServiceMock
            ->expects($this->never())
            ->method('translate');

        $responseMock->expects($this->never())
            ->method('setHtml');
    }

    private function initializeTranslationServiceMockForAriaLabel(string $title): string
    {
        $expectedTranslation = 'Embedded video from YouTube: ' . $title;

        $this->localizationServiceMock
            ->expects($this->once())
            ->method('translate')
            ->with('iframe_aria_label', [self::PROVIDER_NAME, $title])
            ->willReturn($expectedTranslation);

        return $expectedTranslation;
    }
}
