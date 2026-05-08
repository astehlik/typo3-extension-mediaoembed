<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Tests\Unit\Response;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Sto\Mediaoembed\Content\Configuration;
use Sto\Mediaoembed\Response\PhotoResponse;
use TYPO3\CMS\Core\Resource\FileInterface;

#[CoversClass(PhotoResponse::class)]
class PhotoResponseTest extends TestCase
{
    public function testPhotoResponseAccessors(): void
    {
        $fileMock = $this->createMock(FileInterface::class);
        $data = [
            'url' => 'https://example.com/photo.jpg',
            'width' => 100,
            'height' => 200,
            'localFile' => $fileMock,
            'type' => 'photo',
            'version' => '1.0',
        ];

        $response = new PhotoResponse();
        $response->initializeResponseData($data, $this->createMock(Configuration::class));

        $this->assertSame('https://example.com/photo.jpg', $response->getUrl());
        $this->assertSame(100, $response->getWidth());
        $this->assertSame(200, $response->getHeight());
        $this->assertSame($fileMock, $response->getLocalFile());
        $this->assertSame(0.5, $response->getAspectRatio());
    }
}
