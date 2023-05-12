<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Tests\Functional\Request\RequestHandler;

use Sto\Mediaoembed\Domain\Model\Provider;
use Sto\Mediaoembed\Request\RequestHandler\Panopto\PanoptoRequestHandler;
use Sto\Mediaoembed\Tests\Functional\AbstractFunctionalTest;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

final class PanoptoRequestHandlerTest extends AbstractFunctionalTest
{
    public function handleBuildsExpectedIframeDataProvider(): array
    {
        return [
            [
                'https://the-iframe-url.tld',
                'https://the-iframe-url.tld',
            ],
            [
                'https://demo.hosted.panopto.com/Panopto/Pages/Viewer.aspx'
                . '?id=af1d34c39-d435-45456-b5451-a45b045377&offerviewer=false',
                'https://demo.hosted.panopto.com/Panopto/Pages/Embed.aspx'
                . '?offerviewer=false&amp;autoplay=false&amp;id=af1d34c39-d435-45456-b5451-a45b045377',
            ],
        ];
    }

    /**
     * @dataProvider handleBuildsExpectedIframeDataProvider
     */
    public function testHandleBuildsExpectedIframe(string $mediaUrl, string $expectedUrl): void
    {
        $objectManager = $this->getObjectManager();

        $configurationManager = $objectManager->get(ConfigurationManagerInterface::class);
        $configurationManager->setContentObject(new ContentObjectRenderer());
        $configurationManager->getContentObject()->data = ['tx_mediaoembed_url' => $mediaUrl];

        $provider = new Provider(
            'panopto',
            'https://demo.',
            ['https://'],
            false
        );
        $provider->withRequestHandler(
            PanoptoRequestHandler::class,
            [
                'defaultViewerUrlParameters' => [
                    'offerviewer' => 'true',
                    'autoplay' => 'false',
                ],
            ]
        );

        $html = '<iframe'
            . PHP_EOL . '    allow="autoplay"'
            . PHP_EOL . '    allowfullscreen'
            . PHP_EOL . '    src="' . $expectedUrl . '"></iframe>'
            . PHP_EOL;

        $expectedResponse = [
            'type' => 'video',
            'html' => $html,
            'provider_name' => 'Panopto',
        ];

        $requestHandler = $objectManager->get(PanoptoRequestHandler::class);
        self::assertSame($expectedResponse, $requestHandler->handle($provider));
    }

    private function getObjectManager(): ObjectManagerInterface
    {
        return GeneralUtility::makeInstance(ObjectManager::class);
    }
}
