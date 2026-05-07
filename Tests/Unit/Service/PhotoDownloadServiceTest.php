<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Tests\Unit\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Sto\Mediaoembed\Content\Configuration;
use Sto\Mediaoembed\Exception\PhotoDownload\NotAnImageFileException;
use Sto\Mediaoembed\Exception\PhotoDownload\PhotoDownloadException;
use Sto\Mediaoembed\Exception\RequestException;
use Sto\Mediaoembed\Service\HttpService;
use Sto\Mediaoembed\Service\PhotoDownloadService;
use Sto\Mediaoembed\Service\ResourceService;
use Sto\Mediaoembed\Tests\Unit\AbstractUnitTestCase;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\Folder;

#[CoversClass(PhotoDownloadService::class)]
final class PhotoDownloadServiceTest extends AbstractUnitTestCase
{
    private Configuration|MockObject $configurationMock;

    private HttpService|MockObject $httpServiceMock;

    private ResourceService|MockObject $resourceServiceMock;

    private PhotoDownloadService $subject;

    protected function setUp(): void
    {
        $this->httpServiceMock = $this->createMock(HttpService::class);
        $this->resourceServiceMock = $this->createMock(ResourceService::class);
        $this->configurationMock = $this->createMock(Configuration::class);

        $this->subject = new PhotoDownloadService(
            $this->httpServiceMock,
            $this->resourceServiceMock,
        );
    }

    public function testDownloadPhotoDownloadsAndSavesFile(): void
    {
        $downloadUrl = 'https://example.com/photo.jpg';
        $mediaUrl = 'https://example.com/media';
        $imageFilename = sha1($mediaUrl) . '.jpg';
        $tempFile = '/tmp/tempfile';

        $this->setupMocksForDownload($downloadUrl, $mediaUrl, 'image/jpeg', 'jpg', $tempFile);

        $folderMock = $this->createMock(Folder::class);
        $fileMock = $this->createMock(File::class);

        $this->resourceServiceMock->method('getOrCreateFolder')->willReturn($folderMock);
        $folderMock->method('hasFile')->with($imageFilename)->willReturn(false);

        $this->resourceServiceMock->expects($this->once())
            ->method('addFileFromLocal')
            ->with($folderMock, $tempFile, $imageFilename)
            ->willReturn($fileMock);

        $this->assertSame($fileMock, $this->subject->downloadPhoto($downloadUrl, $this->configurationMock));
    }

    public function testDownloadPhotoReturnsExistingFile(): void
    {
        $downloadUrl = 'https://example.com/photo.jpg';
        $mediaUrl = 'https://example.com/media';
        $imageFilename = sha1($mediaUrl) . '.jpg';

        $this->setupMocksForDownload($downloadUrl, $mediaUrl, 'image/jpeg', 'jpg');

        $folderMock = $this->createMock(Folder::class);
        $fileMock = $this->createMock(File::class);

        $this->resourceServiceMock->method('getOrCreateFolder')->willReturn($folderMock);
        $folderMock->method('hasFile')->with($imageFilename)->willReturn(true);
        $folderMock->method('getFile')->with($imageFilename)->willReturn($fileMock);

        $this->assertSame($fileMock, $this->subject->downloadPhoto($downloadUrl, $this->configurationMock));
    }

    public function testDownloadPhotoReturnsNullIfDisabled(): void
    {
        $this->configurationMock->method('isPhotoDownloadEnabled')->willReturn(false);
        $this->assertNull($this->subject->downloadPhoto('https://example.com/photo.jpg', $this->configurationMock));
    }

    public function testDownloadPhotoReturnsNullIfUrlIsEmpty(): void
    {
        $this->assertNull($this->subject->downloadPhoto('', $this->configurationMock));
    }

    public function testDownloadPhotoThrowsExceptionOnRequestError(): void
    {
        $this->configurationMock->method('isPhotoDownloadEnabled')->willReturn(true);
        $this->httpServiceMock->method('getUrl')->willThrowException(new RequestException('Error', 0));

        $this->expectException(PhotoDownloadException::class);
        $this->subject->downloadPhoto('https://example.com/photo.jpg', $this->configurationMock);
    }

    public function testDownloadPhotoWithoutExtension(): void
    {
        $downloadUrl = 'https://example.com/photo';
        $mediaUrl = 'https://example.com/media';
        $imageFilename = sha1($mediaUrl); // No extension

        $this->setupMocksForDownload($downloadUrl, $mediaUrl, 'image/unknown', '');

        $folderMock = $this->createMock(Folder::class);
        $fileMock = $this->createMock(File::class);

        $this->resourceServiceMock->method('getOrCreateFolder')->willReturn($folderMock);
        $folderMock->method('hasFile')->with($imageFilename)->willReturn(true);
        $folderMock->method('getFile')->with($imageFilename)->willReturn($fileMock);

        $this->assertSame($fileMock, $this->subject->downloadPhoto($downloadUrl, $this->configurationMock));
    }

    public function testGetTargetFolder(): void
    {
        $this->configurationMock->method('getPhotoDownloadStorageUid')->willReturn(1);
        $this->configurationMock->method('getPhotoDownloadFolderIdentifier')->willReturn('folder');

        $folderMock = $this->createMock(Folder::class);
        $this->resourceServiceMock->method('getOrCreateFolder')
            ->with(1, 'folder')
            ->willReturn($folderMock);

        $this->assertSame($folderMock, $this->subject->getTargetFolder($this->configurationMock));
    }

    public function testValidateMimeTypeDoesNotThrowForImage(): void
    {
        $this->subject->validateMimeType('https://example.com/image.jpg', 'image/jpeg');
    }

    public function testValidateMimeTypeThrowsExceptionForNonImage(): void
    {
        $this->expectException(NotAnImageFileException::class);
        $this->subject->validateMimeType('https://example.com/file.pdf', 'application/pdf');
    }

    private function setupMocksForDownload(
        string $downloadUrl,
        string $mediaUrl,
        string $mimeType,
        string $extension,
        string $tempFile = '/tmp/tempfile'
    ): void {
        $this->configurationMock->method('isPhotoDownloadEnabled')->willReturn(true);
        $this->configurationMock->method('getMediaUrl')->willReturn($mediaUrl);

        $streamMock = $this->createMock(StreamInterface::class);
        $streamMock->method('getContents')->willReturn('file-contents');

        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock->method('getBody')->willReturn($streamMock);

        $this->httpServiceMock->method('getUrl')->with($downloadUrl)->willReturn($responseMock);

        $this->resourceServiceMock->method('saveTemporaryFile')->with('file-contents')->willReturn($tempFile);
        $this->resourceServiceMock->method('getMimeTypeForLocalFile')->with($tempFile)->willReturn($mimeType);
        $this->resourceServiceMock->method('getFileExtensionByMimeType')->with($mimeType)->willReturn($extension);
    }
}
