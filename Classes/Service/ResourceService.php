<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Service;

use TYPO3\CMS\Core\Resource\Enum\DuplicationBehavior;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\Folder;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Resource\StorageRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;

readonly class ResourceService
{
    public function __construct(
        private StorageRepository $storageRepository,
    ) {
    }

    public function addFile(Folder $folder, string $fileName, string $fileContents): File
    {
        $tempPath = GeneralUtility::tempnam('tx_mediaoembed_');
        GeneralUtility::writeFile($tempPath, $fileContents);

        $file = $folder->addFile($tempPath, $fileName, DuplicationBehavior::RENAME);

        GeneralUtility::unlink_tempfile($tempPath);

        return $file;
    }

    public function getOrCreateFolder(int $storageUid, string $folderIdentifier): Folder
    {
        $storage = $this->storageRepository->getStorageObject($storageUid);

        if ($storage->hasFolder($folderIdentifier)) {
            return $storage->getFolder($folderIdentifier);
        }

        return $storage->createFolder($folderIdentifier);
    }
}
