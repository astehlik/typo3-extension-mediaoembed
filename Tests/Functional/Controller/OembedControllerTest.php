<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Tests\Functional\Controller;

use Sto\Mediaoembed\Tests\Functional\AbstractFunctionalTestCase;
use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalRequest;

class OembedControllerTest extends AbstractFunctionalTestCase
{
    protected array $typoscriptConstantFiles = [
        'EXT:fluid_styled_content/Configuration/TypoScript/constants.typoscript',
        'EXT:mediaoembed/Configuration/TypoScript/constants.txt',
    ];

    private array $typoscriptSetupFilesDefault = [
        'EXT:fluid_styled_content/Configuration/TypoScript/setup.typoscript',
        'EXT:mediaoembed/Tests/Functional/Fixtures/MinimalPage.typoscript',
        'EXT:mediaoembed/Configuration/TypoScript/setup.txt',
        'EXT:mediaoembed/Configuration/TypoScript/DefaultProviders/setup.txt',
        'EXT:mediaoembed/Tests/Functional/Fixtures/Mediaoembed.typoscript',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->importCSVDataSet(__DIR__ . '/../Fixtures/Database/Pages.csv');
        $this->importCSVDataSet(__DIR__ . '/../Fixtures/Database/ContentElements.csv');
        $this->setUpFrontendRootPage(
            1,
            [
                'setup' => $this->typoscriptSetupFilesDefault,
                'constants' => $this->typoscriptConstantFiles,
            ]
        );
        $this->setUpFrontendSite(1);
    }

    public function testPanoptoDirectLinkIsNotRendered(): void
    {
        $expectedDirectLink = '<a rel="noreferrer noopener" target="_blank"'
            . ' href="https://demo.hosted.panopto.com/Panopto';

        self::assertStringNotContainsString($expectedDirectLink, $this->renderOembedContent(4));
    }

    public function testPanoptoViewerIsConvertedToEmbed(): void
    {
        $expectedIframeUrl =
            ' src="https://demo.hosted.panopto.com/Panopto/Pages/Embed.aspx'
            . '?autoplay=false&amp;offerviewer=true&amp;showtitle=false&amp;'
            . 'showbrand=false&amp;start=0&amp;interactivity=all&amp;'
            . 'id=24573-4a48-4688c-965a-036878978a0fb';

        self::assertStringContainsString($expectedIframeUrl, $this->renderOembedContent(4));
    }

    public function testYouTubeDirectLinkIsRendered(): void
    {
        $expectedDirectLink = '<a rel="noreferrer noopener" target="_blank"'
            . ' href="https://www.youtube.com/watch?v=iwGFalTRHDA">';

        self::assertStringContainsString($expectedDirectLink, $this->renderOembedContent());
    }

    public function testYouTubeIframeIsRendered(): void
    {
        $expectedIframe = '<iframe width="459" height="344"'
            . ' src="https://www.youtube-nocookie.com/embed/iwGFalTRHDA?feature=oembed&amp;rel=1"'
            . ' frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"'
            . ' allowfullscreen aria-label="YouTube media: Trololo"></iframe>';

        self::assertStringContainsString($expectedIframe, $this->renderOembedContent());
    }

    public function testYouTubeIframeIsRenderedWithoutRelated(): void
    {
        $expectedIframe = '<iframe width="459" height="344"'
            . ' src="https://www.youtube-nocookie.com/embed/iwGFalTRHDA?feature=oembed&amp;rel=0"'
            . ' frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"'
            . ' allowfullscreen aria-label="YouTube media: Trololo"></iframe>';

        $this->renderOembedContent(3);

        self::assertStringContainsString($expectedIframe, $this->renderOembedContent(3));
    }

    private function renderOembedContent(int $openPageUid = 2): string
    {
        $request = (new InternalRequest())->withPageId($openPageUid)->withLanguageId(0);
        $response = $this->executeFrontendSubRequest($request);
        return (string)$response->getBody();
    }
}
