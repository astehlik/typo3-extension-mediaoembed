<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Resource;

use Exception;
use Sto\Mediaoembed\Content\ConfigurationFactory;
use Sto\Mediaoembed\Domain\Model\ResolverResult;
use Sto\Mediaoembed\Response\HtmlAwareResponseInterface;
use Sto\Mediaoembed\Service\ResolverService;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Resource\OnlineMedia\Helpers\OnlineMediaHelperInterface;
use TYPO3\CMS\Core\Resource\OnlineMedia\Helpers\OnlineMediaHelperRegistry;
use TYPO3\CMS\Core\Resource\Rendering\FileRendererInterface;

class OembedRenderer implements FileRendererInterface
{
    private OnlineMediaHelperInterface|false|null $onlineMediaHelper = null;

    public function __construct(
        private readonly ConfigurationFactory $configurationFactory,
        private readonly OnlineMediaHelperRegistry $onlineMediaHelperRegistry,
        private readonly ResolverService $resolverService
    ) {
    }

    public function canRender(FileInterface $file): bool
    {
        return ($file->getExtension() === 'oembed') && $this->getOnlineMediaHelper($file) !== false;
    }

    public function getPriority(): int
    {
        return 100;
    }

    public function render(FileInterface $file, $width, $height, array $options = [])
    {
        $orgFile = $file;
        if ($orgFile instanceof FileReference) {
            $orgFile = $orgFile->getOriginalFile();
        }

        $mediaUrl = $this->getOnlineMediaHelper($file)->getOnlineMediaId($orgFile);

        $result = $this->getOembedResult($mediaUrl);

        if ($result === null) {
            return '';
        }

        $response = $result->getResponse();

        if ($response instanceof HtmlAwareResponseInterface) {
            return $response->getHtml();
        }

        return $mediaUrl;
    }

    private function getOembedResult(string $url): ?ResolverResult
    {
        try {
            $configuration = $this->configurationFactory->createForUrl($url);
            $response = $this->resolverService->resolve($configuration);
        } catch (Exception) {
            return null;
        }

        return $response;
    }

    private function getOnlineMediaHelper(FileInterface $file): OnlineMediaHelperInterface|false
    {
        if ($this->onlineMediaHelper !== null) {
            return $this->onlineMediaHelper;
        }

        $this->loadOnlineMediaHelper($file);

        if ($this->onlineMediaHelper === null) {
            throw new \RuntimeException('Could not load online media helper');
        }

        return $this->onlineMediaHelper;
    }

    private function loadOnlineMediaHelper(FileInterface $file): void
    {
        $orgFile = $file;
        if ($orgFile instanceof FileReference) {
            $orgFile = $orgFile->getOriginalFile();
        }

        if (!$orgFile instanceof File) {
            $this->onlineMediaHelper = false;
            return;
        }

        $this->onlineMediaHelper = $this->onlineMediaHelperRegistry->getOnlineMediaHelper($orgFile);
    }
}
