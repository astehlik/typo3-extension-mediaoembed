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
     * @var MockObject|AspectRatioCalculatorInterface
     */
    private $aspectRatioCalculatorMock;

    /**
     * @var AspectRatioEvaluation
     */
    private $aspectRatioEvaluation;

    protected function setUp()
    {
        $this->aspectRatioEvaluation = new AspectRatioEvaluation();

        $this->aspectRatioCalculatorMock = $this->createMock(AspectRatioCalculatorInterface::class);
        $this->aspectRatioEvaluation->injectAspectRatioCalculator($this->aspectRatioCalculatorMock);
    }

    /**
     * @test
     * @dataProvider deevaluateFieldValueReturnsExpectedValueDataProvider
     *
     * @param array $parameters
     * @param string $expectedValue
     */
    public function deevaluateFieldValueReturnsExpectedValue(array $parameters, string $expectedValue)
    {
        $this->assertEquals($expectedValue, $this->aspectRatioEvaluation->deevaluateFieldValue($parameters));
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

    /**
     * @test
     * @dataProvider evaluateFieldValueReturnsEmptyStringForInvalidValuesDataProvider
     *
     * @param mixed $value
     */
    public function evaluateFieldValueReturnsEmptyStringForInvalidValues($value)
    {
        if ($value === 'invalid') {
            $this->aspectRatioCalculatorMock->method('isValidAspectRatio')
                ->with('invalid')
                ->willReturn(false);
        }

        $this->assertEquals('', $this->aspectRatioEvaluation->evaluateFieldValue($value));
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
     * @test
     */
    public function evaluateFieldValueReturnsValueForvalueValue()
    {
        $this->aspectRatioCalculatorMock->method('isValidAspectRatio')
            ->with('valid')
            ->willReturn(true);

        $this->assertEquals('valid', $this->aspectRatioEvaluation->evaluateFieldValue('valid'));
    }

    public function testReturnFieldJSReturnsJsContents()
    {
        $this->assertStringEqualsFile(
            __DIR__ . '/../../../Classes/Backend/AspectRatioEvaluation.js',
            $this->aspectRatioEvaluation->returnFieldJS()
        );
    }
}
