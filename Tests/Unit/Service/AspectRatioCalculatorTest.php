<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Tests\Unit\Service;

use Sto\Mediaoembed\Service\AspectRatioCalculator;
use Sto\Mediaoembed\Tests\Unit\AbstractUnitTest;

final class AspectRatioCalculatorTest extends AbstractUnitTest
{
    /**
     * @var AspectRatioCalculator
     */
    private $aspectRatioCalculator;

    public function setUp()
    {
        $this->aspectRatioCalculator = new AspectRatioCalculator();
    }

    /**
     * @test
     * @dataProvider calculateAspectRatioReturnsExpectedValuesDataProvider
     *
     * @param float $expectedValue
     * @param string $aspectRatio
     */
    public function calculateAspectRatioReturnsExpectedValues(float $expectedValue, string $aspectRatio)
    {
        $this->assertEquals($expectedValue, $this->aspectRatioCalculator->calculateAspectRatio($aspectRatio));
    }

    public function calculateAspectRatioReturnsExpectedValuesDataProvider(): array
    {
        return [
            'invalid value' => [0.0, 'asdf'],
            'valid value' => [2, '10:5'],
            'valid value 2' => [0.5, '5:10'],
        ];
    }

    /**
     * @test
     * @dataProvider isValidAspectRatioReturnsFalseForInvalidValuesDataProvider
     *
     * @param string $value
     */
    public function isValidAspectRatioReturnsFalseForInvalidValues(string $value)
    {
        $this->assertFalse($this->aspectRatioCalculator->isValidAspectRatio($value));
    }

    public function isValidAspectRatioReturnsFalseForInvalidValuesDataProvider(): array
    {
        return [
            'regex not matching' => ['asdf'],
            'width is zero' => ['0:10'],
            'width is invalid' => ['01:10'],
            'height is zero' => ['10:0'],
            'height is invalid' => ['10:01'],
        ];
    }

    /**
     * @test
     */
    public function isValidAspectRatioReturnsTrueForValidValue()
    {
        $this->assertTrue($this->aspectRatioCalculator->isValidAspectRatio('10:5'));
    }
}
