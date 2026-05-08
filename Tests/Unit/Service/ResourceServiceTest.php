<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Tests\Unit\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use ReflectionClass;
use RuntimeException;
use Sto\Mediaoembed\Service\ResourceService;
use Sto\Mediaoembed\Tests\Unit\AbstractUnitTestCase;
use TYPO3\CMS\Core\Resource\Enum\DuplicationBehavior;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\Folder;
use TYPO3\CMS\Core\Resource\MimeTypeDetector;
use TYPO3\CMS\Core\Resource\ResourceStorage;
use TYPO3\CMS\Core\Resource\StorageRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;

#[CoversClass(ResourceService::class)]
final class ResourceServiceTest extends AbstractUnitTestCase
{
    private MimeTypeDetector $mimeTypeDetector;

    private StorageRepository|MockObject $storageRepositoryMock;

    private ResourceService $subject;

    protected function setUp(): void
    {
        $this->mimeTypeDetector = new MimeTypeDetector();
        $this->storageRepositoryMock = $this->createMock(StorageRepository::class);

        $this->subject = new ResourceService(
            $this->mimeTypeDetector,
            $this->storageRepositoryMock,
        );
    }

    public function testAddFileFromLocal(): void
    {
        $folderMock = $this->createMock(Folder::class);
        $fileMock = $this->createMock(File::class);

        $folderMock->expects($this->once())
            ->method('addFile')
            ->with('/local/path', 'file.jpg', DuplicationBehavior::REPLACE)
            ->willReturn($fileMock);

        $this->assertSame($fileMock, $this->subject->addFileFromLocal($folderMock, '/local/path', 'file.jpg'));
    }

    public function testCleanupTemporaryFiles(): void
    {
        $tempFile = $this->subject->saveTemporaryFile('test');
        $this->assertFileExists($tempFile);

        $reflection = new ReflectionClass($this->subject);
        $method = $reflection->getMethod('cleanupTemporaryFiles');
        $method->invoke($this->subject);

        $this->assertFileDoesNotExist($tempFile);
    }

    public function testGetFileExtensionByMimeType(): void
    {
        $this->assertSame('jpg', $this->subject->getFileExtensionByMimeType('image/jpeg'));
    }

    public function testGetFileExtensionByMimeTypeReturnsEmptyStringIfNotFound(): void
    {
        $this->assertSame('', $this->subject->getFileExtensionByMimeType('unknown/mime'));
    }

    public function testGetMimeTypeForLocalFile(): void
    {
        $tempFile = GeneralUtility::tempnam('test');
        GeneralUtility::writeFile($tempFile, 'test');

        try {
            $this->assertSame('text/plain', $this->subject->getMimeTypeForLocalFile($tempFile));
        } finally {
            GeneralUtility::unlink_tempfile($tempFile);
        }
    }

    public function testGetOrCreateFolderCreatesNewFolder(): void
    {
        $storageUid = 1;
        $folderIdentifier = 'new-folder';

        $storageMock = $this->createMock(ResourceStorage::class);
        $folderMock = $this->createMock(Folder::class);

        $this->storageRepositoryMock->method('getStorageObject')
            ->with($storageUid)
            ->willReturn($storageMock);

        $storageMock->method('hasFolder')->with($folderIdentifier)->willReturn(false);
        $storageMock->method('createFolder')->with($folderIdentifier)->willReturn($folderMock);

        $this->assertSame($folderMock, $this->subject->getOrCreateFolder($storageUid, $folderIdentifier));
    }

    public function testGetOrCreateFolderReturnsExistingFolder(): void
    {
        $storageUid = 1;
        $folderIdentifier = 'test-folder';

        $storageMock = $this->createMock(ResourceStorage::class);
        $folderMock = $this->createMock(Folder::class);

        $this->storageRepositoryMock->method('getStorageObject')
            ->with($storageUid)
            ->willReturn($storageMock);

        $storageMock->method('hasFolder')->with($folderIdentifier)->willReturn(true);
        $storageMock->method('getFolder')->with($folderIdentifier)->willReturn($folderMock);

        $this->assertSame($folderMock, $this->subject->getOrCreateFolder($storageUid, $folderIdentifier));
    }

    public function testSaveTemporaryFile(): void
    {
        $content = 'temporary-content';
        $tempFile = $this->subject->saveTemporaryFile($content);

        $this->assertFileExists($tempFile);
        $this->assertSame($content, file_get_contents($tempFile));

        // Cleanup manually for the test
        GeneralUtility::unlink_tempfile($tempFile);
    }

    public function testSaveTemporaryFileThrowsExceptionOnFailure(): void
    {
        /** @var MockObject|ResourceService $subject */
        $subject = $this->getMockBuilder(ResourceService::class)
            ->setConstructorArgs([$this->mimeTypeDetector, $this->storageRepositoryMock])
            ->onlyMethods(['writeFile'])
            ->getMock();

        $subject->method('writeFile')->willReturn(false);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Could not write temporary file');

        $subject->saveTemporaryFile('content');
    }
}
