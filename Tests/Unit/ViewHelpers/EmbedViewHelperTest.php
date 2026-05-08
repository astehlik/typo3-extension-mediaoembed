<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Tests\Unit\ViewHelpers;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use Sto\Mediaoembed\Content\Configuration;
use Sto\Mediaoembed\Response\LinkResponse;
use Sto\Mediaoembed\Response\VideoResponse;
use Sto\Mediaoembed\Service\ViewFactory;
use Sto\Mediaoembed\Tests\Unit\AbstractUnitTestCase;
use Sto\Mediaoembed\ViewHelpers\EmbedViewHelper;
use TYPO3\CMS\Core\View\ViewInterface;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

#[CoversClass(EmbedViewHelper::class)]
class EmbedViewHelperTest extends AbstractUnitTestCase
{
    private MockObject|Configuration $configurationMock;

    private MockObject|RenderingContextInterface $renderingContextMock;

    private EmbedViewHelper $subject;

    private MockObject|ViewFactory $viewFactoryMock;

    private MockObject|ViewInterface $viewMock;

    protected function setUp(): void
    {
        $this->viewMock = $this->createMock(ViewInterface::class);

        $this->viewFactoryMock = $this->createMock(ViewFactory::class);
        $this->viewFactoryMock->method('createChildView')->willReturn($this->viewMock);

        $this->renderingContextMock = $this->createMock(RenderingContextInterface::class);

        $this->subject = new EmbedViewHelper($this->viewFactoryMock);
        $this->subject->setRenderingContext($this->renderingContextMock);
        $this->subject->setRenderChildrenClosure(static fn() => 'child-content');

        $this->configurationMock = $this->createMock(Configuration::class);
        $this->configurationMock->method('getAspectRatio')->with(16 / 9)->willReturn(16 / 9);
        $this->configurationMock->method('getEmbedResponsiveClass')->willReturn('ratio');

        $responseMock = $this->createMock(VideoResponse::class);
        $responseMock->method('getAspectRatio')->willReturn(16 / 9);

        $this->subject->setArguments([
            'configuration' => $this->configurationMock,
            'response' => $responseMock,
            'style-property' => 'padding-top',
        ]);
    }

    public function testRenderWithConsent(): void
    {
        $this->configurationMock->method('isConsentEnabled')->willReturn(true);

        $this->viewFactoryMock->expects($this->once())
            ->method('createChildView')
            ->with($this->renderingContextMock)
            ->willReturn($this->viewMock);

        $expectedResult = '<div class="ratio" style="padding-top: 56.25%%;" data-oembed-html="%s" data-provider="" />';
        $expectedResult = sprintf($expectedResult, base64_encode('child-content'));

        $this->assertSame($expectedResult, $this->subject->initializeArgumentsAndRender());
    }

    public function testRenderWithInvalidResponseFails(): void
    {
        $this->subject->setArguments([
            'configuration' => $this->configurationMock,
            'response' => $this->createMock(LinkResponse::class),
            'style-property' => 'padding-top',
        ]);

        $this->expectExceptionMessage('Response must implement AspectRatioAwareResponseInterface');

        $this->subject->initializeArgumentsAndRender();
    }

    public function testRenderWithoutConsent(): void
    {
        $this->configurationMock->method('isConsentEnabled')->willReturn(false);

        $result = $this->subject->initializeArgumentsAndRender();

        $this->assertSame('<div class="ratio" style="padding-top: 56.25%;">child-content</div>', $result);
    }
}
