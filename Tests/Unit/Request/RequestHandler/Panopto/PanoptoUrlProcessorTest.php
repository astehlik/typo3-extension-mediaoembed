<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Tests\Unit\Request\RequestHandler\Panopto;

use PHPUnit\Framework\Attributes\CoversClass;
use Sto\Mediaoembed\Request\RequestHandler\Panopto\PanoptoUrlProcessor;
use Sto\Mediaoembed\Service\UrlService;
use Sto\Mediaoembed\Tests\Unit\AbstractUnitTestCase;

#[CoversClass(PanoptoUrlProcessor::class)]
final class PanoptoUrlProcessorTest extends AbstractUnitTestCase
{
    /**
     * @var PanoptoUrlProcessor
     */
    private $urlProcessor;

    protected function setUp(): void
    {
        $this->urlProcessor = new PanoptoUrlProcessor(new UrlService());
    }

    public function testProcessUrlWithEmbedUrlKeepsUrls(): void
    {
        $url = 'https://demo.hosted.panopto.com/Panopto/Pages/Embed.aspx'
            . '?id=af1d3c39-d8455-41456-b005-ad52435b77'
            . '&autoplay=false&offerviewer=true&showtitle=true&showbrand=false&start=0&interactivity=all';
        $newUrl = $this->urlProcessor->processUrl($url, ['bla' => 'blubb']);
        $this->assertSame($url, $newUrl);
    }

    public function testProcessUrlWithUnknownUrlKeepsUrls(): void
    {
        $newUrl = $this->urlProcessor->processUrl('https://www.some-url.tld', ['bla' => 'blubb']);
        $this->assertSame('https://www.some-url.tld', $newUrl);
    }

    public function testProcessUrlWithViewerUrlConvertsItToEmbedUrl(): void
    {
        $url = 'https://demo.hosted.panopto.com/Panopto/Pages/Viewer.aspx'
            . '?id=af1d34c39-d435-45456-b5451-a45b045377&offerviewer=false';
        $parameters = [
            'offerviewer' => 'true',
            'autoplay' => 'false',
        ];
        $newUrl = $this->urlProcessor->processUrl($url, $parameters);
        $expectedUrl = 'https://demo.hosted.panopto.com/Panopto/Pages/Embed.aspx'
            . '?offerviewer=false&autoplay=false&id=af1d34c39-d435-45456-b5451-a45b045377';
        $this->assertSame($expectedUrl, $newUrl);
    }
}
