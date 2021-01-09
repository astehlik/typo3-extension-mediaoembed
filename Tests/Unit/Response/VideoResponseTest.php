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
    public function setUp()
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

    /**
     * @test
     * @dataProvider aspectRatioIsDetectedCorrectlyDataProvider
     * @param int $width
     * @param int $height
     * @param string $expectedRatioType
     */
    public function aspectRatioTypeIsDetectedCorrectly($width, $height, $expectedRatioType)
    {
        $this->videoResponse->initializeResponseData(
            [
                'type' => 'video',
                'html' => '<embed />',
                'width' => $width,
                'height' => $height,
            ]
        );
        $this->assertEquals($expectedRatioType, $this->videoResponse->getAspectRatioType());

        $is4To3 = $expectedRatioType === VideoResponse::ASPECT_RATIO_4TO3;
        $is16To9 = $expectedRatioType === VideoResponse::ASPECT_RATIO_16TO9;
        $this->assertEquals($is4To3, $this->videoResponse->getAspectRatioIs4To3());
        $this->assertEquals($is16To9, $this->videoResponse->getAspectRatioIs16To9());
    }

    /**
     * @test
     */
    public function getAspectRatioTypeReturnsWidthDividedByHeight()
    {
        $this->videoResponse->initializeResponseData(
            [
                'type' => 'video',
                'html' => '<embed />',
                'width' => 160,
                'height' => 190,
            ]
        );
        $this->assertEquals(160 / 190, $this->videoResponse->getAspectRatio());
    }

    public function testAspectRatioDoesNotDivideByZero()
    {
        $this->assertEquals(0, $this->videoResponse->getAspectRatio());
    }

    public function testSetHtml()
    {
        $this->videoResponse->setHtml('the new html');
        $this->assertEquals('the new html', $this->videoResponse->getHtml());
    }
}
