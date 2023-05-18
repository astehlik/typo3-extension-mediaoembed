<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Resource;

use Sto\Mediaoembed\Content\ConfigurationFactory;
use Sto\Mediaoembed\Domain\Model\ResolverResult;
use Sto\Mediaoembed\Exception\ProviderResolveFailedException;
use Sto\Mediaoembed\Response\Contract\AspectRatioAwareResponseInterface;
use Sto\Mediaoembed\Response\GenericResponse;
use Sto\Mediaoembed\Service\ResolverService;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\Folder;
use TYPO3\CMS\Core\Resource\OnlineMedia\Helpers\AbstractOnlineMediaHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class OembedHelper extends AbstractOnlineMediaHelper
{
    private ?ConfigurationFactory $configurationFactory = null;

    private ?ResolverService $resolverService = null;

    public function getMetaData(File $file): array
    {
        $metadata = [];

        $oEmbedResult = $this->getOembedResponse($this->getOnlineMediaId($file));

        if ($oEmbedResult === null) {
            return $metadata;
        }

        $response = $oEmbedResult->getResponse();

        if ($response instanceof AspectRatioAwareResponseInterface) {
            $metadata['width'] = $response->getWidth();
            $metadata['height'] = $response->getHeight();
        }

        $metadata['title'] = strip_tags($response->getTitle());

        $metadata['author'] = strip_tags($response->getAuthorName());

        return $metadata;
    }

    public function getPreviewImage(File $file): string
    {
        $oEmbedResult = $this->getOembedResponse($this->getOnlineMediaId($file));

        if ($oEmbedResult === null) {
            return '';
        }

        $response = $oEmbedResult->getResponse();

        if ($response->getThumbnailUrl() === '') {
            return '';
        }

        $fileName = 'oembed_' . md5($response->getConfiguration()->getMediaUrl()) . '.jpg';
        $temporaryFileName = $this->getTempFolderPath() . $fileName;

        if (file_exists($temporaryFileName)) {
            return $temporaryFileName;
        }

        $previewImage = GeneralUtility::getUrl($response->getThumbnailUrl());

        if ($previewImage !== false) {
            file_put_contents($temporaryFileName, $previewImage);
            GeneralUtility::fixPermissions($temporaryFileName);
        }

        return $temporaryFileName;
    }

    public function getPublicUrl(File $file): ?string
    {
        return $this->getOnlineMediaId($file);
    }

    public function transformUrlToFile($url, Folder $targetFolder): ?File
    {
        if (!is_string($url) && $url === '') {
            return null;
        }

        $oembedResponse = $this->getOembedResponse($url);

        if ($oembedResponse === null) {
            return null;
        }

        return $this->transformMediaIdToFile($url, $oembedResponse->getResponse(), $targetFolder);
    }

    private function getConfigurationFactory(): ConfigurationFactory
    {
        if ($this->configurationFactory === null) {
            $this->configurationFactory = GeneralUtility::makeInstance(ConfigurationFactory::class);
        }
        return $this->configurationFactory;
    }

    private function getOembedResponse(string $url): ?ResolverResult
    {
        try {
            $configuration = $this->getConfigurationFactory()->createForUrl($url);
            $response = $this->getResolverService()->resolve($configuration);
        } catch (ProviderResolveFailedException $e) {
            return null;
        }

        return $response;
    }

    private function getResolverService(): ResolverService
    {
        if ($this->resolverService === null) {
            $this->resolverService = GeneralUtility::makeInstance(ResolverService::class);
        }
        return $this->resolverService;
    }

    private function transformMediaIdToFile(string $url, GenericResponse $oembedResponse, Folder $targetFolder): File
    {
        $file = $this->findExistingFileByOnlineMediaId($url, $targetFolder, $this->extension);

        if ($file !== null) {
            return $file;
        }

        if ($oembedResponse->getTitle() !== '') {
            $fileName = $oembedResponse->getTitle() . '.' . $this->extension;
        } else {
            $fileName = $url . '.' . $this->extension;
        }

        return $this->createNewFile($targetFolder, $fileName, $url);
    }
}
