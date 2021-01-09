<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Service;

use SplFileInfo;
use Sto\Mediaoembed\Exception\PhotoDownload\NotAnImageFileException;
use Sto\Mediaoembed\Exception\PhotoDownloadException;
use Sto\Mediaoembed\Exception\RequestException;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\Folder;

class PhotoDownloadService
{
    /**
     * @var ConfigurationService
     */
    private $configurationService;

    /**
     * @var HttpService
     */
    private $httpService;

    /**
     * @var ResourceService
     */
    private $resourceService;

    public function __construct(
        ConfigurationService $configurationService,
        HttpService $httpService,
        ResourceService $resourceService
    ) {
        $this->configurationService = $configurationService;
        $this->httpService = $httpService;
        $this->resourceService = $resourceService;
    }

    /**
     * Downloads the photo from the server and stores it in the typo3temp folder.
     *
     * @param string $embedUrl The URL specified by the editor that should be embedded.
     * @param string $downloadUrl The media URL returned by the oEmbed Service.
     * @return File|null
     */
    public function downloadPhoto(string $embedUrl, string $downloadUrl)
    {
        if (!$downloadUrl) {
            return null;
        }

        if (!$this->configurationService->isPhotoDownloadEnabled()) {
            return null;
        }

        try {
            $response = $this->httpService->getUrl($downloadUrl);
        } catch (RequestException $e) {
            throw new PhotoDownloadException($downloadUrl, $e);
        }

        $imageFilename = sha1($embedUrl);
        $extension = $this->detectExtension($downloadUrl);
        if ($extension) {
            $imageFilename .= '.' . $extension;
        }

        $targetFolder = $this->getTargetFolder();

        if ($targetFolder->hasFile($imageFilename)) {
            return $this->resourceService->getFileInFolder($targetFolder, $imageFilename);
        }

        $file = $this->resourceService->addFile($targetFolder, $imageFilename, $response->getBody()->getContents());

        $this->validateMimeType($downloadUrl, $file);

        return $file;
    }

    /**
     * @return Folder
     */
    public function getTargetFolder(): Folder
    {
        $targetFolder = $this->resourceService->getOrCreateFolder(
            $this->configurationService->getPhotoDownloadStorageUid(),
            $this->configurationService->getPhotoDownloadFolderIdentifier()
        );
        return $targetFolder;
    }

    /**
     * @param string $downloadUrl
     * @param File $file
     * @throws NotAnImageFileException
     */
    public function validateMimeType(string $downloadUrl, File $file)
    {
        if ($file->getType() !== File::FILETYPE_IMAGE) {
            $mimeType = $file->getMimeType();
            $file->delete();
            throw new NotAnImageFileException($downloadUrl, $mimeType);
        }
    }

    private function detectExtension(string $photoUrl): string
    {
        $fileInfo = new SplFileInfo($photoUrl);
        return $fileInfo->getExtension();
    }
}
