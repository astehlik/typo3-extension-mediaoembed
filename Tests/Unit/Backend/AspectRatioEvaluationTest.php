<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Tests\Unit\Backend;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use Sto\Mediaoembed\Backend\AspectRatioEvaluation;
use Sto\Mediaoembed\Service\AspectRatioCalculatorInterface;
use Sto\Mediaoembed\Tests\Unit\AbstractUnitTestCase;
use TYPO3\CMS\Core\Page\JavaScriptModuleInstruction;

final class AspectRatioEvaluationTest extends AbstractUnitTestCase
{
    private AspectRatioCalculatorInterface|MockObject $aspectRatioCalculatorMock;

    private AspectRatioEvaluation $aspectRatioEvaluation;

    protected function setUp(): void
    {
        $this->aspectRatioEvaluation = new AspectRatioEvaluation();

        $this->aspectRatioCalculatorMock = $this->createMock(AspectRatioCalculatorInterface::class);
        $this->aspectRatioEvaluation->injectAspectRatioCalculator($this->aspectRatioCalculatorMock);
    }

    public static function provideDeevaluateFieldValueReturnsExpectedValueCases(): iterable
    {
        return [
            [
                [],
                '',
            ],
            [
                ['value' => 'test'],
                'test',
            ],
        ];
    }

    public static function provideEvaluateFieldValueReturnsEmptyStringForInvalidValuesCases(): iterable
    {
        return [
            [null],
            [''],
            [' '],
            ['invalid'],
        ];
    }

    #[DataProvider('provideDeevaluateFieldValueReturnsExpectedValueCases')]
    public function testDeevaluateFieldValueReturnsExpectedValue(array $parameters, string $expectedValue): void
    {
        self::assertSame($expectedValue, $this->aspectRatioEvaluation->deevaluateFieldValue($parameters));
    }

    #[DataProvider('provideEvaluateFieldValueReturnsEmptyStringForInvalidValuesCases')]
    public function testEvaluateFieldValueReturnsEmptyStringForInvalidValues(mixed $value): void
    {
        if ($value === 'invalid') {
            $this->aspectRatioCalculatorMock->method('isValidAspectRatio')
                ->with('invalid')
                ->willReturn(false);
        }

        self::assertSame('', $this->aspectRatioEvaluation->evaluateFieldValue($value));
    }

    public function testEvaluateFieldValueReturnsValueForvalueValue(): void
    {
        $this->aspectRatioCalculatorMock->method('isValidAspectRatio')
            ->with('valid')
            ->willReturn(true);

        self::assertSame('valid', $this->aspectRatioEvaluation->evaluateFieldValue('valid'));
    }

    public function testReturnFieldJSReturnsJsContents(): void
    {
        self::assertInstanceOf(
            JavaScriptModuleInstruction::class,
            $this->aspectRatioEvaluation->returnFieldJS(),
        );
    }
}
