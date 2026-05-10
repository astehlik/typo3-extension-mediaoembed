<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Tests\Unit\ViewHelpers;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use Sto\Mediaoembed\Content\Configuration;
use Sto\Mediaoembed\Response\LinkResponse;
use Sto\Mediaoembed\Response\VideoResponse;
use Sto\Mediaoembed\Tests\Unit\AbstractUnitTestCase;
use Sto\Mediaoembed\ViewHelpers\Behavior\EmbedResponsiveTrait;
use Sto\Mediaoembed\ViewHelpers\EmbedResponsivePaddingViewHelper;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

#[CoversClass(EmbedResponsiveTrait::class)]
#[CoversClass(EmbedResponsivePaddingViewHelper::class)]
class EmbedResponsivePaddingViewHelperTest extends AbstractUnitTestCase
{
    private MockObject|Configuration $configurationMock;

    private MockObject|RenderingContextInterface $renderingContextMock;

    private MockObject|VideoResponse $responseMock;

    private EmbedResponsivePaddingViewHelper $subject;

    protected function setUp(): void
    {
        $this->renderingContextMock = $this->createMock(RenderingContextInterface::class);

        $this->subject = new EmbedResponsivePaddingViewHelper();
        $this->subject->setRenderingContext($this->renderingContextMock);
        $this->subject->setRenderChildrenClosure(static fn() => 'child-content');

        $this->configurationMock = $this->createMock(Configuration::class);
        $this->responseMock = $this->createMock(VideoResponse::class);

        $this->subject->setArguments([
            'configuration' => $this->configurationMock,
            'response' => $this->responseMock,
        ]);
    }

    public function testRender(): void
    {
        $this->expectConfigurationCalls();

        $result = $this->subject->initializeArgumentsAndRender();

        $this->assertSame('<div class="ratio" style="--bs-aspect-ratio: 56.25%;">child-content</div>', $result);
    }

    public function testRenderWithCustomAspectRatio(): void
    {
        $this->expectConfigurationCalls('ratio', '--bs-aspect-ratio', 4 / 3, 1.0);

        $result = $this->subject->initializeArgumentsAndRender();

        $this->assertSame('<div class="ratio" style="--bs-aspect-ratio: 100%;">child-content</div>', $result);
    }

    public function testRenderWithCustomResponsiveConfiguration(): void
    {
        $this->expectConfigurationCalls('custom-ratio', '--custom-aspect-ratio');

        $result = $this->subject->initializeArgumentsAndRender();

        $this->assertSame(
            '<div class="custom-ratio" style="--custom-aspect-ratio: 56.25%;">child-content</div>',
            $result
        );
    }

    public function testRenderWithInvalidResponseFails(): void
    {
        $this->subject->setArguments([
            'configuration' => $this->configurationMock,
            'response' => $this->createMock(LinkResponse::class),
        ]);

        $this->expectExceptionMessage('Response must implement AspectRatioAwareResponseInterface');

        $this->subject->initializeArgumentsAndRender();
    }

    public function testRenderWithMultipleClasses(): void
    {
        $this->expectConfigurationCalls('ratio ratio-16x9');

        $result = $this->subject->initializeArgumentsAndRender();

        $this->assertSame(
            '<div class="ratio ratio-16x9" style="--bs-aspect-ratio: 56.25%;">child-content</div>',
            $result
        );
    }

    private function expectConfigurationCalls(
        string $class = 'ratio',
        string $styleProperty = '--bs-aspect-ratio',
        float $responseAspectRatio = 16 / 9,
        ?float $aspectRatioValue = null
    ): void {
        $this->configurationMock
            ->expects($this->once())
            ->method('getEmbedResponsiveClass')
            ->willReturn($class);
        $this->configurationMock
            ->expects($this->once())
            ->method('getEmbedResponsiveStyleProperty')
            ->willReturn($styleProperty);

        $expectedAspectRatio = $aspectRatioValue ?? $responseAspectRatio;
        $this->configurationMock->expects($this->once())
            ->method('getAspectRatio')
            ->with($responseAspectRatio)
            ->willReturn($expectedAspectRatio);

        $this->responseMock->expects($this->atLeastOnce())->method('getAspectRatio')->willReturn($responseAspectRatio);
    }
}
