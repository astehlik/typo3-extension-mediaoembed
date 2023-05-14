<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Tests\Unit\Response;

/*                                                                        *
 * This script belongs to the TYPO3 Extension "mediaoembed".              *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use Sto\Mediaoembed\Content\Configuration;
use Sto\Mediaoembed\Response\VideoResponse;
use Sto\Mediaoembed\Tests\Unit\AbstractUnitTest;

/**
 * Tests for the VideoResponse.
 */
class VideoResponseTest extends AbstractUnitTest
{
    /**
     * @var VideoResponse
     */
    protected $videoResponse;

    /**
     * Initialies the test subject.
     */
    protected function setUp(): void
    {
        $this->videoResponse = new VideoResponse();
    }

    /**
     * @return array
     */
    public function aspectRatioIsDetectedCorrectlyDataProvider()
    {
        return [
            '16 to 9 ratio' => [
                160,
                90,
                VideoResponse::ASPECT_RATIO_16TO9,
            ],
            '4 to 3 ratio' => [
                400,
                300,
                VideoResponse::ASPECT_RATIO_4TO3,
            ],
            'near 16 to 9 ratio' => [
                400,
                200,
                VideoResponse::ASPECT_RATIO_16TO9,
            ],
            'near 4 to 3 ratio' => [
                160,
                150,
                VideoResponse::ASPECT_RATIO_4TO3,
            ],
        ];
    }

    public function testAspectRatioDoesNotDivideByZero(): void
    {
        self::assertSame(0.0, $this->videoResponse->getAspectRatio());
    }

    /**
     * @dataProvider aspectRatioIsDetectedCorrectlyDataProvider
     *
     * @param int $width
     * @param int $height
     * @param string $expectedRatioType
     */
    public function testAspectRatioTypeIsDetectedCorrectly($width, $height, $expectedRatioType): void
    {
        $this->videoResponse->initializeResponseData(
            [
                'type' => 'video',
                'html' => '<embed />',
                'width' => $width,
                'height' => $height,
            ],
            $this->createMock(Configuration::class)
        );
        self::assertSame($expectedRatioType, $this->videoResponse->getAspectRatioType());

        $is4To3 = $expectedRatioType === VideoResponse::ASPECT_RATIO_4TO3;
        $is16To9 = $expectedRatioType === VideoResponse::ASPECT_RATIO_16TO9;
        self::assertSame($is4To3, $this->videoResponse->getAspectRatioIs4To3());
        self::assertSame($is16To9, $this->videoResponse->getAspectRatioIs16To9());
    }

    public function testGetAspectRatioTypeReturnsWidthDividedByHeight(): void
    {
        $this->videoResponse->initializeResponseData(
            [
                'type' => 'video',
                'html' => '<embed />',
                'width' => 160,
                'height' => 190,
            ],
            $this->createMock(Configuration::class)
        );
        self::assertSame(160 / 190, $this->videoResponse->getAspectRatio());
    }

    public function testSetHtml(): void
    {
        $this->videoResponse->setHtml('the new html');
        self::assertSame('the new html', $this->videoResponse->getHtml());
    }
}
