<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Tests\Functional\Request\RequestHandler;

use Sto\Mediaoembed\Domain\Model\Provider;
use Sto\Mediaoembed\Request\RequestHandler\PanoptoRequestHandler;
use Sto\Mediaoembed\Tests\Functional\AbstractFunctionalTest;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

final class PanoptoRequestHandlerTest extends AbstractFunctionalTest
{
    public function testHandle()
    {
        $objectManager = $this->getObjectManager();

        $configurationManager = $objectManager->get(ConfigurationManagerInterface::class);
        $configurationManager->setContentObject(new ContentObjectRenderer());
        $configurationManager->getContentObject()->data = ['tx_mediaoembed_url' => 'https://the-iframe-url.tld'];

        $provider = new Provider(
            'panopto',
            'https://demo.',
            ['https://'],
            false,
            PanoptoRequestHandler::class
        );

        $html = '<iframe'
            . PHP_EOL . '    allow="autoplay"'
            . PHP_EOL . '    allowfullscreen'
            . PHP_EOL . '    src="https://the-iframe-url.tld"></iframe>'
            . PHP_EOL;

        $expectedResponse = [
            'type' => 'video',
            'html' => $html,
            'provider_name' => 'Panopto',
        ];

        $requestHandler = $objectManager->get(PanoptoRequestHandler::class);
        $this->assertEquals($expectedResponse, $requestHandler->handle($provider));
    }

    private function getObjectManager(): ObjectManagerInterface
    {
        return GeneralUtility::makeInstance(ObjectManager::class);
    }
}
