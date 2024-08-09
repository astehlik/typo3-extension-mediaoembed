<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Tests\Unit\Service;

use PHPUnit\Framework\Attributes\DataProvider;
use Sto\Mediaoembed\Service\AspectRatioCalculator;
use Sto\Mediaoembed\Tests\Unit\AbstractUnitTestCase;

final class AspectRatioCalculatorTest extends AbstractUnitTestCase
{
    private AspectRatioCalculator $aspectRatioCalculator;

    protected function setUp(): void
    {
        $this->aspectRatioCalculator = new AspectRatioCalculator();
    }

    public static function provideCalculateAspectRatioReturnsExpectedValuesCases(): iterable
    {
        return [
            'invalid value' => [
                0.0,
                'asdf',
            ],
            'valid value' => [
                2,
                '10:5',
            ],
            'valid value 2' => [
                0.5,
                '5:10',
            ],
        ];
    }

    public static function provideIsValidAspectRatioReturnsFalseForInvalidValuesCases(): iterable
    {
        return [
            'regex not matching' => ['asdf'],
            'width is zero' => ['0:10'],
            'width is invalid' => ['01:10'],
            'height is zero' => ['10:0'],
            'height is invalid' => ['10:01'],
        ];
    }

    #[DataProvider('provideCalculateAspectRatioReturnsExpectedValuesCases')]
    public function testCalculateAspectRatioReturnsExpectedValues(float $expectedValue, string $aspectRatio): void
    {
        self::assertSame($expectedValue, $this->aspectRatioCalculator->calculateAspectRatio($aspectRatio));
    }

    #[DataProvider('provideIsValidAspectRatioReturnsFalseForInvalidValuesCases')]
    public function testIsValidAspectRatioReturnsFalseForInvalidValues(string $value): void
    {
        self::assertFalse($this->aspectRatioCalculator->isValidAspectRatio($value));
    }

    public function testIsValidAspectRatioReturnsTrueForValidValue(): void
    {
        self::assertTrue($this->aspectRatioCalculator->isValidAspectRatio('10:5'));
    }
}
