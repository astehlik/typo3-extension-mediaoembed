<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Service;

use Sto\Mediaoembed\Content\Configuration;
use Sto\Mediaoembed\Exception\PhotoDownload\NotAnImageFileException;
use Sto\Mediaoembed\Exception\PhotoDownloadException;
use Sto\Mediaoembed\Exception\RequestException;
use TYPO3\CMS\Core\Resource\AbstractFile;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Resource\Folder;

class PhotoDownloadService
{
    private HttpService $httpService;

    private ResourceService $resourceService;

    public function __construct(
        HttpService $httpService,
        ResourceService $resourceService
    ) {
        $this->httpService = $httpService;
        $this->resourceService = $resourceService;
    }

    /**
     * Downloads the photo from the server and stores it in the typo3temp folder.
     *
     * @param string $downloadUrl the media URL returned by the oEmbed Service
     */
    public function downloadPhoto(string $downloadUrl, Configuration $configuration): ?FileInterface
    {
        if (!$downloadUrl) {
            return null;
        }

        if (!$configuration->isPhotoDownloadEnabled()) {
            return null;
        }

        try {
            /** @extensionScannerIgnoreLine  */
            $response = $this->httpService->getUrl($downloadUrl);
        } catch (RequestException $e) {
            throw new PhotoDownloadException($downloadUrl, $e);
        }

        $imageFilename = sha1($configuration->getMediaUrl());
        $extension = $this->detectExtension($downloadUrl);
        if ($extension) {
            $imageFilename .= '.' . $extension;
        }

        $targetFolder = $this->getTargetFolder($configuration);

        if ($targetFolder->hasFile($imageFilename)) {
            return $this->resourceService->getFileInFolder($targetFolder, $imageFilename);
        }

        $file = $this->resourceService->addFile($targetFolder, $imageFilename, $response->getBody()->getContents());

        $this->validateMimeType($downloadUrl, $file);

        return $file;
    }

    public function getTargetFolder(Configuration $configuration): Folder
    {
        return $this->resourceService->getOrCreateFolder(
            $configuration->getPhotoDownloadStorageUid(),
            $configuration->getPhotoDownloadFolderIdentifier()
        );
    }

    public function validateMimeType(string $downloadUrl, File $file): void
    {
        if ($file->getType() !== AbstractFile::FILETYPE_IMAGE) {
            $mimeType = $file->getMimeType();
            $file->delete();
            throw new NotAnImageFileException($downloadUrl, $mimeType);
        }
    }

    private function detectExtension(string $photoUrl): string
    {
        $fileInfo = new \SplFileInfo($photoUrl);
        return $fileInfo->getExtension();
    }
}
