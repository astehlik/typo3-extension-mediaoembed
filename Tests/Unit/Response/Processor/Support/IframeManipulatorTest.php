<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Tests\Unit\Response\Processor\Support;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use Sto\Mediaoembed\Exception\ProcessorException;
use Sto\Mediaoembed\Response\HtmlAwareResponseInterface;
use Sto\Mediaoembed\Response\Processor\Support\IframeManipulator;
use Sto\Mediaoembed\Service\UrlService;
use Sto\Mediaoembed\Tests\Unit\AbstractUnitTestCase;
use TYPO3\CMS\Core\Http\Uri;

#[CoversClass(IframeManipulator::class)]
final class IframeManipulatorTest extends AbstractUnitTestCase
{
    private IframeManipulator $subject;

    protected function setUp(): void
    {
        $this->subject = new IframeManipulator(new UrlService());
    }

    public function testAddIframeAttributeIfNonExistingAddsNewAttribute(): void
    {
        $responseMock = $this->expectModifiedHtml(
            '<iframe src="https://example.com"></iframe>',
            '<iframe src="https://example.com" title="test-title"></iframe>'
        );

        $this->subject->addIframeAttributeIfNonExisting($responseMock, 'title', 'test-title');
    }

    public function testAddIframeAttributeIfNonExistingDoesNotOverrideExistingAttribute(): void
    {
        $html = '<iframe src="https://example.com" title="existing-title"></iframe>';
        $responseMock = $this->createMock(HtmlAwareResponseInterface::class);
        $responseMock->method('getHtml')->willReturn($html);
        $responseMock->expects($this->never())->method('setHtml');

        $this->subject->addIframeAttributeIfNonExisting($responseMock, 'title', 'new-title');
    }

    public function testAddIframeAttributeIfNonExistingOverridesEmptyAttribute(): void
    {
        $html = '<iframe src="https://example.com" title=""></iframe>';
        $expectedHtml = '<iframe src="https://example.com" title="new-title"></iframe>';
        $responseMock = $this->expectModifiedHtml($html, $expectedHtml);

        $this->subject->addIframeAttributeIfNonExisting($responseMock, 'title', 'new-title');
    }

    public function testModifyIframeUrlHandlesNullSrc(): void
    {
        $responseMock = $this->expectModifiedHtml(
            '<iframe></iframe>',
            '<iframe src="https://my-new-src"></iframe>'
        );

        $urlModifier = function (?Uri $uri): Uri {
            if ($uri instanceof Uri) {
                $this->fail('Expected null URI');
            }

            return new Uri('https://my-new-src');
        };

        $this->subject->modifyIframeUrl($responseMock, $urlModifier);
    }

    public function testModifyIframeUrlModifiesSrcAttribute(): void
    {
        $responseMock = $this->expectModifiedHtml(
            '<iframe src="https://example.com/video"></iframe>',
            '<iframe src="https://modified.com/video"></iframe>'
        );

        $urlModifier = static fn(?Uri $url) => $url->withHost('modified.com');
        $this->subject->modifyIframeUrl($responseMock, $urlModifier);
    }

    public function testModifyIframeUrlRemovesSrcIfEmpty(): void
    {
        $responseMock = $this->expectModifiedHtml(
            '<iframe src="https://example.com/video"></iframe>',
            '<iframe></iframe>'
        );

        $urlModifier = static fn(?Uri $url) => null;
        $this->subject->modifyIframeUrl($responseMock, $urlModifier);
    }

    public function testModifyIframeUrlThrowsExceptionOnInvalidHtml(): void
    {
        $html = '<span>not an iframe</span>';
        $responseMock = $this->createMock(HtmlAwareResponseInterface::class);
        $responseMock->method('getHtml')->willReturn($html);

        $this->expectException(ProcessorException::class);
        $this->expectExceptionMessage('Expected HTML to be iframe');

        $urlModifier = static fn(?string $url) => $url;
        $this->subject->modifyIframeUrl($responseMock, $urlModifier);
    }

    private function expectModifiedHtml(
        string $html,
        string $expectedHtml
    ): HtmlAwareResponseInterface|MockObject {
        $responseMock = $this->createMock(HtmlAwareResponseInterface::class);
        $responseMock->method('getHtml')->willReturn($html);
        $responseMock
            ->expects($this->once())
            ->method('setHtml')
            ->with($expectedHtml);

        return $responseMock;
    }
}
