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
use Sto\Mediaoembed\ViewHelpers\Behavior\EmbedResponsiveTrait;
use Sto\Mediaoembed\ViewHelpers\EmbedViewHelper;
use TYPO3\CMS\Core\View\ViewInterface;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

#[CoversClass(EmbedResponsiveTrait::class)]
#[CoversClass(EmbedViewHelper::class)]
class EmbedViewHelperTest extends AbstractUnitTestCase
{
    private array $arguments;

    private MockObject|Configuration $configurationMock;

    private MockObject|RenderingContextInterface $renderingContextMock;

    private MockObject|VideoResponse $responseMock;

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
        $this->responseMock = $this->createMock(VideoResponse::class);

        $this->arguments = [
            'configuration' => $this->configurationMock,
            'response' => $this->responseMock,
        ];
        $this->subject->setArguments($this->arguments);
    }

    public function testRenderWithClassFromAdditinalArguments(): void
    {
        $this->expectConfigurationCalls();

        $this->subject->setArguments($this->arguments + ['additionalAttributes' => ['class' => 'my-custom-class']]);

        $result = $this->subject->initializeArgumentsAndRender();

        $this->assertSame(
            '<div class="my-custom-class ratio" style="--bs-aspect-ratio: 56.25%;">child-content</div>',
            $result
        );
    }

    public function testRenderWithConsent(): void
    {
        $this->expectConfigurationCalls(true);
        $this->responseMock
            ->expects($this->once())
            ->method('getProviderName')
            ->willReturn('the-provider');

        $this->viewFactoryMock->expects($this->once())
            ->method('createChildView')
            ->with($this->renderingContextMock)
            ->willReturn($this->viewMock);

        $this->viewMock->expects($this->once())
            ->method('assignMultiple')
            ->with([
                'response' => $this->responseMock,
                'configuration' => $this->configurationMock,
                'previewHtml' => 'child-content',
            ]);
        $this->viewMock->expects($this->once())
            ->method('render')
            ->with('Consent/Placeholder')
            ->willReturn('placeholder-content');

        $expectedResult = '<div class="ratio" style="--bs-aspect-ratio: 56.25%%;" data-oembed-html="%s"'
            . ' data-provider="the-provider">placeholder-content</div>';
        $expectedResult = sprintf($expectedResult, base64_encode('child-content'));

        $this->assertSame($expectedResult, $this->subject->initializeArgumentsAndRender());
    }

    public function testRenderWithCustomAspectRatio(): void
    {
        $this->expectConfigurationCalls(false, 'ratio', '--bs-aspect-ratio', 4 / 3, 1.0);

        $result = $this->subject->initializeArgumentsAndRender();

        $this->assertSame('<div class="ratio" style="--bs-aspect-ratio: 100%;">child-content</div>', $result);
    }

    public function testRenderWithCustomResponsiveConfiguration(): void
    {
        $this->expectConfigurationCalls(false, 'custom-ratio', '--custom-aspect-ratio');

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
        $this->expectConfigurationCalls(false, 'ratio ratio-16x9');

        $result = $this->subject->initializeArgumentsAndRender();

        $this->assertSame(
            '<div class="ratio ratio-16x9" style="--bs-aspect-ratio: 56.25%;">child-content</div>',
            $result
        );
    }

    public function testRenderWithoutConsent(): void
    {
        $this->expectConfigurationCalls();

        $result = $this->subject->initializeArgumentsAndRender();

        $this->assertSame(
            '<div class="ratio" style="--bs-aspect-ratio: 56.25%;">child-content</div>',
            $result
        );
    }

    public function testRenderWithStyleFromAdditinalArguments(): void
    {
        $this->expectConfigurationCalls();

        $this->subject->setArguments($this->arguments + ['additionalAttributes' => ['style' => 'width: 100%;']]);

        $result = $this->subject->initializeArgumentsAndRender();

        $this->assertSame(
            '<div style="width: 100%; --bs-aspect-ratio: 56.25%;" class="ratio">child-content</div>',
            $result
        );
    }

    private function expectConfigurationCalls(
        bool $isConsentEnabled = false,
        string $class = 'ratio',
        string $styleProperty = '--bs-aspect-ratio',
        float $responseAspectRatio = 16 / 9,
        ?float $aspectRatioValue = null
    ): void {
        $this->configurationMock
            ->expects($this->once())
            ->method('isConsentEnabled')
            ->willReturn($isConsentEnabled);
        $this->configurationMock
            ->expects($this->once())
            ->method('getEmbedResponsiveClass')
            ->willReturn($class);
        $this->configurationMock
            ->expects($this->once())
            ->method('getEmbedResponsiveStyleProperty')
            ->willReturn($styleProperty);

        $expectedAspectRatio = $aspectRatioValue ?? $responseAspectRatio;
        $this->configurationMock
            ->expects($this->once())
            ->method('getAspectRatio')
            ->with($responseAspectRatio)
            ->willReturn($expectedAspectRatio);

        $this->responseMock->expects($this->atLeastOnce())->method('getAspectRatio')->willReturn($responseAspectRatio);
    }
}
