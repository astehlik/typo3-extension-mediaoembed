<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Tests\Functional;

use Symfony\Component\Yaml\Yaml;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

abstract class AbstractFunctionalTest extends FunctionalTestCase
{
    protected $testExtensionsToLoad = ['typo3conf/ext/mediaoembed'];

    /**
     * Create a simple site config for the tests that
     * call a frontend page.
     */
    protected function setUpFrontendSite(int $pageId, array $additionalLanguages = []): void
    {
        $languages = [
            [
                'title' => 'English',
                'enabled' => true,
                'languageId' => 0,
                'base' => '/',
                'typo3Language' => 'default',
                'locale' => 'en_US.UTF-8',
                'iso-639-1' => 'en',
                'navigationTitle' => '',
                'hreflang' => '',
                'direction' => '',
                'flag' => 'us',
            ],
            [
                'title' => 'German',
                'enabled' => true,
                'languageId' => 1,
                'base' => '/de/',
                'typo3Language' => 'de',
                'locale' => 'de_DE.UTF-8',
                'iso-639-1' => 'de',
                'navigationTitle' => '',
                'hreflang' => '',
                'direction' => '',
                'flag' => 'de',
            ],
        ];
        $languages = array_merge($languages, $additionalLanguages);
        $configuration = [
            'rootPageId' => $pageId,
            'base' => 'https://website.local/',
            'languages' => $languages,
            'errorHandling' => [],
            'routes' => [],
        ];
        GeneralUtility::mkdir_deep($this->instancePath . '/typo3conf/sites/testing/');
        $yamlFileContents = Yaml::dump($configuration, 99, 2);
        $fileName = $this->instancePath . '/typo3conf/sites/testing/config.yaml';
        GeneralUtility::writeFile($fileName, $yamlFileContents);
        // Ensure that no other site configuration was cached before
        $cache = GeneralUtility::makeInstance(CacheManager::class)->getCache('core');
        if ($cache->has('site-configuration')) {
            $cache->remove('site-configuration');
        }
    }
}
