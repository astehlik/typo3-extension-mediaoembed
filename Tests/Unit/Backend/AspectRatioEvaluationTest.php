<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Tests\Unit\Backend;

use PHPUnit\Framework\MockObject\MockObject;
use Sto\Mediaoembed\Backend\AspectRatioEvaluation;
use Sto\Mediaoembed\Service\AspectRatioCalculatorInterface;
use Sto\Mediaoembed\Tests\Unit\AbstractUnitTestCase;

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

    public static function deevaluateFieldValueReturnsExpectedValueDataProvider(): array
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

    public static function evaluateFieldValueReturnsEmptyStringForInvalidValuesDataProvider(): array
    {
        return [
            [null],
            [''],
            [' '],
            ['invalid'],
        ];
    }

    /**
     * @dataProvider deevaluateFieldValueReturnsExpectedValueDataProvider
     */
    public function testDeevaluateFieldValueReturnsExpectedValue(array $parameters, string $expectedValue): void
    {
        self::assertSame($expectedValue, $this->aspectRatioEvaluation->deevaluateFieldValue($parameters));
    }

    /**
     * @dataProvider evaluateFieldValueReturnsEmptyStringForInvalidValuesDataProvider
     */
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
        self::assertStringEqualsFile(
            __DIR__ . '/../../../Classes/Backend/AspectRatioEvaluation.js',
            $this->aspectRatioEvaluation->returnFieldJS()
        );
    }
}
