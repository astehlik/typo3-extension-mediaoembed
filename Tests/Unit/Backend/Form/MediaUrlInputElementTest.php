<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Tests\Unit\Backend\Form;

use Sto\Mediaoembed\Backend\Form\MediaUrlInputElement;
use Sto\Mediaoembed\Service\UtilityService;
use Sto\Mediaoembed\Tests\Unit\AbstractUnitTestCase;
use TYPO3\CMS\Backend\Form\NodeFactory;
use TYPO3\CMS\Core\Page\JavaScriptModuleInstruction;

final class MediaUrlInputElementTest extends AbstractUnitTestCase
{
    public function testAddUrlParserJsToResultRequiresJsModules(): void
    {
        $field = $this->createInputField();

        $result = ['html' => ''];
        $result = $field->addUrlParserJsToResult($result);

        self::assertCount(1, $result['javaScriptModules']);

        $moduleInstruction = $result['javaScriptModules'][0];
        self::assertInstanceOf(JavaScriptModuleInstruction::class, $moduleInstruction);

        $expectedInstanceItem = [
            'type' => JavaScriptModuleInstruction::ITEM_INSTANCE,
            'args' => ['some-random-string'],
        ];
        $instanceItem = $moduleInstruction->getItems()[0];

        self::assertSame($expectedInstanceItem, $instanceItem);
    }

    public function testAddUrlParserJsToResultWrapsContainerWithId(): void
    {
        $field = $this->createInputField();

        $result = ['html' => '<div id="inner">test</div>'];
        $result = $field->addUrlParserJsToResult($result);

        self::assertSame('<div id="some-random-string"><div id="inner">test</div></div>', $result['html']);
    }

    private function createInputField(): MediaUrlInputElement
    {
        $nodeFactoryMock = $this->createMock(NodeFactory::class);

        $utilityService = $this->createMock(UtilityService::class);
        $utilityService->expects(self::once())
            ->method('getUniqueId')
            ->with('tx-mediaoembed-url-input-wrapper-')
            ->willReturn('some-random-string');

        $field = new MediaUrlInputElement();

        $field->injectUtilityService($utilityService);
        return $field;
    }
}
