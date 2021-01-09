<?php

namespace Sto\Mediaoembed\Tests\Functional\Controller;

use Sto\Mediaoembed\Tests\Functional\AbstractFunctionalTest;

class OembedControllerTest extends AbstractFunctionalTest
{
    protected function setUp()
    {
        parent::setUp();

        $this->importDataSet('ntf://Database/pages.xml');
        $this->importDataSet(__DIR__ . '/../Fixtures/ContentElements.xml');
        $this->setUpFrontendRootPage(
            1,
            [
                'EXT:mediaoembed/Tests/Functional/Fixtures/MinimalPage.typoscript',
                'EXT:mediaoembed/Configuration/TypoScript/setup.txt',
                'EXT:mediaoembed/Configuration/TypoScript/DefaultProviders/setup.txt',
                'EXT:mediaoembed/Tests/Functional/Fixtures/Mediaoembed.typoscript',
            ]
        );
    }

    public function testYouTubeDirectLinkIsRendered()
    {
        $expectedDirectLink = '<a rel="noreferrer noopener" target="_blank"'
            . ' href="https://www.youtube.com/watch?v=iwGFalTRHDA">';

        $this->assertContains($expectedDirectLink, $this->renderOembedContent());
    }

    public function testYouTubeIframeIsRendered()
    {
        $expectedIframe = '<iframe width="459" height="344"'
            . ' src="https://www.youtube-nocookie.com/embed/iwGFalTRHDA?feature=oembed&amp;rel=1"'
            . ' frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"'
            . ' allowfullscreen></iframe>';

        $this->assertContains($expectedIframe, $this->renderOembedContent());
    }

    public function testYouTubeIframeIsRenderedWithoutRelated()
    {
        $expectedIframe = '<iframe width="459" height="344"'
            . ' src="https://www.youtube-nocookie.com/embed/iwGFalTRHDA?feature=oembed&amp;rel=0"'
            . ' frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"'
            . ' allowfullscreen></iframe>';

        $this->renderOembedContent(3);

        $this->assertContains($expectedIframe, $this->renderOembedContent(3));
    }

    private function renderOembedContent(int $openPageUid = 2): string
    {
        $response = $this->getFrontendResponse($openPageUid);
        return $response->getContent();
    }
}
