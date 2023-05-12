<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Tests\Unit\Backend;

use PHPUnit\Framework\MockObject\MockObject;
use Sto\Mediaoembed\Backend\AspectRatioEvaluation;
use Sto\Mediaoembed\Service\AspectRatioCalculatorInterface;
use Sto\Mediaoembed\Tests\Unit\AbstractUnitTest;

final class AspectRatioEvaluationTest extends AbstractUnitTest
{
    /**
     * @var AspectRatioCalculatorInterface|MockObject
     */
    private $aspectRatioCalculatorMock;

    /**
     * @var AspectRatioEvaluation
     */
    private $aspectRatioEvaluation;

    protected function setUp(): void
    {
        $this->aspectRatioEvaluation = new AspectRatioEvaluation();

        $this->aspectRatioCalculatorMock = $this->createMock(AspectRatioCalculatorInterface::class);
        $this->aspectRatioEvaluation->injectAspectRatioCalculator($this->aspectRatioCalculatorMock);
    }

    public function deevaluateFieldValueReturnsExpectedValueDataProvider(): array
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

    public function evaluateFieldValueReturnsEmptyStringForInvalidValuesDataProvider(): array
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
     *
     * @param mixed $value
     */
    public function testEvaluateFieldValueReturnsEmptyStringForInvalidValues($value): void
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
