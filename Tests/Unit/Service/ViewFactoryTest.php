<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Tests\Unit\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Constraint\Callback;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Http\Message\ServerRequestInterface;
use Sto\Mediaoembed\Service\ViewFactory;
use Sto\Mediaoembed\Tests\Unit\AbstractUnitTestCase;
use TYPO3\CMS\Core\View\ViewFactoryData;
use TYPO3\CMS\Core\View\ViewFactoryInterface;
use TYPO3\CMS\Core\View\ViewInterface;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\View\TemplatePaths;

#[CoversClass(ViewFactory::class)]
class ViewFactoryTest extends AbstractUnitTestCase
{
    private ViewFactory $subject;

    private ViewFactoryInterface|MockObject $viewFactoryMock;

    protected function setUp(): void
    {
        $this->viewFactoryMock = $this->createMock(ViewFactoryInterface::class);

        $this->subject = new ViewFactory($this->viewFactoryMock);
    }

    public function testCreateChildViewFailsIfRenderingContextIsMissing(): void
    {
        $this->expectExceptionMessage('Rendering context is required');

        $this->subject->createChildView(null);
    }

    public function testCreateChildViewSetsPaths(): void
    {
        $templatePaths = new TemplatePaths();
        $templatePaths->setTemplateRootPaths(['/other-ext/Templates/']);
        $templatePaths->setPartialRootPaths(['/other-ext/Partials/']);
        $templatePaths->setLayoutRootPaths(['/other-ext/Layouts/']);

        $renderingContextMock = $this->createRenderingContextMock($templatePaths);

        $callback = $this->callback(function (ViewFactoryData $viewFactoryData) {
            $this->assertSame(['/other-ext/Templates/'], $viewFactoryData->templateRootPaths);
            $this->assertSame(['/other-ext/Partials/'], $viewFactoryData->partialRootPaths);
            $this->assertSame(['/other-ext/Layouts/'], $viewFactoryData->layoutRootPaths);
            $this->assertNull($viewFactoryData->request);
            return true;
        });

        $this->callCreateView($callback, $renderingContextMock);
    }

    public function testCreateChildViewSetsRequest(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);

        $renderingContextMock = $this->createRenderingContextMock(new TemplatePaths(), $request);

        $callback = $this->callback(function (ViewFactoryData $viewFactoryData) use ($request) {
            $this->assertSame($request, $viewFactoryData->request);
            return true;
        });

        $this->callCreateView($callback, $renderingContextMock);
    }

    private function callCreateView(
        Callback $callback,
        RenderingContextInterface|MockObject $renderingContextMock
    ): void {
        $viewMock = $this->createMock(ViewInterface::class);

        $this->viewFactoryMock->expects($this->once())
            ->method('create')
            ->with($callback)
            ->willReturn($viewMock);

        $result = $this->subject->createChildView($renderingContextMock);

        $this->assertSame($viewMock, $result);
    }

    private function createRenderingContextMock(
        TemplatePaths $templatePaths,
        ServerRequestInterface|MockObject|null $request = null
    ): MockObject|RenderingContextInterface {
        $renderingContextMock = $this->createMock(RenderingContextInterface::class);
        $renderingContextMock
            ->expects($this->once())
            ->method('getTemplatePaths')
            ->willReturn($templatePaths);
        $renderingContextMock
            ->expects($this->once())
            ->method('hasAttribute')
            ->with(ServerRequestInterface::class)
            ->willReturn($request !== null);

        if ($request !== null) {
            $renderingContextMock
                ->expects($this->once())
                ->method('getAttribute')
                ->with(ServerRequestInterface::class)
                ->willReturn($request);
        }

        return $renderingContextMock;
    }
}
