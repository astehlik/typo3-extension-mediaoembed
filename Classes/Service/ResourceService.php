<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Service;

use RuntimeException;
use TYPO3\CMS\Core\Resource\Enum\DuplicationBehavior;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\Folder;
use TYPO3\CMS\Core\Resource\MimeTypeDetector;
use TYPO3\CMS\Core\Resource\StorageRepository;
use TYPO3\CMS\Core\Type\File\FileInfo;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ResourceService
{
    private array $temporaryFiles = [];

    public function __construct(
        private readonly MimeTypeDetector $mimeTypeDetector,
        private readonly StorageRepository $storageRepository,
    ) {
        register_shutdown_function([$this, 'cleanupTemporaryFiles']);
    }

    public function addFileFromLocal(Folder $folder, string $localPath, string $fileName): File
    {
        return $folder->addFile($localPath, $fileName, DuplicationBehavior::REPLACE);
    }

    public function getFileExtensionByMimeType(string $mimeType): string
    {
        return $this->mimeTypeDetector->getFileExtensionsForMimeType($mimeType)[0] ?? '';
    }

    public function getMimeTypeForLocalFile(string $localFilePath): string
    {
        $fileInfo = GeneralUtility::makeInstance(FileInfo::class, $localFilePath);

        return $fileInfo->getMimeType();
    }

    public function getOrCreateFolder(int $storageUid, string $folderIdentifier): Folder
    {
        /** @extensionScannerIgnoreLine - False positive */
        $storage = $this->storageRepository->getStorageObject($storageUid);

        if ($storage->hasFolder($folderIdentifier)) {
            return $storage->getFolder($folderIdentifier);
        }

        return $storage->createFolder($folderIdentifier);
    }

    public function saveTemporaryFile(string $fileContents): string
    {
        $tempPath = GeneralUtility::tempnam('tx_mediaoembed_');

        if (!$this->writeFile($tempPath, $fileContents)) {
            throw new RuntimeException('Could not write temporary file');
        }

        $this->temporaryFiles[] = $tempPath;

        return $tempPath;
    }

    protected function writeFile(string $path, string $content): bool
    {
        return GeneralUtility::writeFile($path, $content);
    }

    /**
     * @codeCoverageIgnore
     */
    private function cleanupTemporaryFiles(): void
    {
        foreach ($this->temporaryFiles as $temporaryFile) {
            GeneralUtility::unlink_tempfile($temporaryFile);
        }
    }
}
