<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Tests\Unit\Backend\Form;

use Sto\Mediaoembed\Backend\Form\MediaUrlInputElement;
use Sto\Mediaoembed\Service\UtilityService;
use Sto\Mediaoembed\Tests\Unit\AbstractUnitTest;
use TYPO3\CMS\Backend\Form\NodeFactory;

final class MediaUrlInputElementTest extends AbstractUnitTest
{
    public function testAddUrlParserJsToResultRequiresJsModules(): void
    {
        $field = $this->createInputField();

        $result = ['html' => ''];
        $result = $field->addUrlParserJsToResult($result);

        self::assertCount(1, $result['requireJsModules']);
        $expectedJs = 'function(UrlParser) { new UrlParser(\'some-random-string\'); }';
        self::assertSame(['TYPO3/CMS/Mediaoembed/Backend/UrlParser' => $expectedJs], $result['requireJsModules'][0]);
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

        MediaUrlInputElement::$testMode = true;
        $field = new MediaUrlInputElement($nodeFactoryMock, []);
        MediaUrlInputElement::$testMode = false;

        $field->injectUtilityService($utilityService);
        return $field;
    }
}
