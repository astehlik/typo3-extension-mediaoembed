<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Service;

use Sto\Mediaoembed\Content\Configuration;
use Sto\Mediaoembed\Exception\PhotoDownload\NotAnImageFileException;
use Sto\Mediaoembed\Exception\PhotoDownload\PhotoDownloadException;
use Sto\Mediaoembed\Exception\RequestException;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Resource\FileType;
use TYPO3\CMS\Core\Resource\Folder;

readonly class PhotoDownloadService
{
    public function __construct(
        private HttpService $httpService,
        private ResourceService $resourceService,
    ) {}

    /**
     * Downloads the photo from the server and stores it in the typo3temp folder.
     *
     * @param string $downloadUrl the media URL returned by the oEmbed Service
     */
    public function downloadPhoto(string $downloadUrl, Configuration $configuration): ?FileInterface
    {
        if ($downloadUrl === '') {
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
        $temporaryFile = $this->resourceService->saveTemporaryFile($response->getBody()->getContents());

        $mimeType = $this->resourceService->getMimeTypeForLocalFile($temporaryFile);
        $this->validateMimeType($downloadUrl, $mimeType);

        $extension = $this->resourceService->getFileExtensionByMimeType($mimeType);
        if ($extension !== '') {
            $imageFilename .= '.' . $extension;
        }

        $targetFolder = $this->getTargetFolder($configuration);

        if ($targetFolder->hasFile($imageFilename)) {
            return $targetFolder->getFile($imageFilename);
        }

        return $this->resourceService->addFileFromLocal($targetFolder, $temporaryFile, $imageFilename);
    }

    public function getTargetFolder(Configuration $configuration): Folder
    {
        return $this->resourceService->getOrCreateFolder(
            $configuration->getPhotoDownloadStorageUid(),
            $configuration->getPhotoDownloadFolderIdentifier(),
        );
    }

    public function validateMimeType(string $downloadUrl, string $mimeType): void
    {
        $fileType = FileType::tryFromMimeType($mimeType);

        if ($fileType !== FileType::IMAGE) {
            throw new NotAnImageFileException($downloadUrl, $mimeType);
        }
    }
}
