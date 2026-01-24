<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Tests\Functional\Backend\Form;

use Sto\Mediaoembed\Backend\Form\MediaUrlInputElement;
use Sto\Mediaoembed\Service\UtilityService;
use Sto\Mediaoembed\Tests\Functional\AbstractFunctionalTestCase;
use TYPO3\CMS\Backend\Form\NodeFactory;
use TYPO3\CMS\Core\Page\JavaScriptModuleInstruction;

final class MediaUrlInputElementTest extends AbstractFunctionalTestCase
{
    public function testAddUrlParserJsToResultRequiresJsModules(): void
    {
        $field = $this->createInputField();

        $result = ['html' => ''];
        $result = $field->addUrlParserJsToResult($result);

        $this->assertCount(1, $result['javaScriptModules']);

        $moduleInstruction = $result['javaScriptModules'][0];
        $this->assertInstanceOf(JavaScriptModuleInstruction::class, $moduleInstruction);

        $expectedInstanceItem = [
            'type' => JavaScriptModuleInstruction::ITEM_INSTANCE,
            'args' => ['some-random-string'],
        ];
        $instanceItem = $moduleInstruction->getItems()[0];

        $this->assertSame($expectedInstanceItem, $instanceItem);
    }

    public function testAddUrlParserJsToResultWrapsContainerWithId(): void
    {
        $field = $this->createInputField();

        $result = ['html' => '<div id="inner">test</div>'];
        $result = $field->addUrlParserJsToResult($result);

        $this->assertSame('<div id="some-random-string"><div id="inner">test</div></div>', $result['html']);
    }

    private function createInputField(): MediaUrlInputElement
    {
        $nodeFactoryMock = $this->createMock(NodeFactory::class);

        $utilityService = $this->createMock(UtilityService::class);
        $utilityService->expects($this->once())
            ->method('getUniqueId')
            ->with('tx-mediaoembed-url-input-wrapper-')
            ->willReturn('some-random-string');

        $field = new MediaUrlInputElement();

        $field->injectUtilityService($utilityService);
        return $field;
    }
}
