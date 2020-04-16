<?php
declare(strict_types=1);

namespace Sto\Mediaoembed\Service;

use TYPO3\CMS\Core\Resource\DuplicationBehavior;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\Folder;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ResourceService
{
    /**
     * @var ResourceFactory
     */
    private $resourceFactory;

    public function __construct(ResourceFactory $resourceFactory)
    {
        $this->resourceFactory = $resourceFactory;
    }

    public function addFile(Folder $folder, string $fileName, string $fileContents): File
    {
        $tempPath = GeneralUtility::tempnam('tx_mediaoembed_');
        GeneralUtility::writeFile($tempPath, $fileContents);

        $file = $folder->addFile($tempPath, $fileName, DuplicationBehavior::RENAME);

        GeneralUtility::unlink_tempfile($tempPath);

        return $file;
    }

    public function getFileInFolder(Folder $targetFolder, string $imageFilename): File
    {
        $storage = $this->resourceFactory->getStorageObject($targetFolder->getStorage()->getUid());
        return $storage->getFileInFolder($imageFilename, $targetFolder);
    }

    public function getOrCreateFolder($storageUid, $folderIdentifier): Folder
    {
        $storage = $this->resourceFactory->getStorageObject($storageUid);
        if ($storage->hasFolder($folderIdentifier)) {
            return $storage->getFolder($folderIdentifier);
        }

        return $storage->createFolder($folderIdentifier);
    }
}
