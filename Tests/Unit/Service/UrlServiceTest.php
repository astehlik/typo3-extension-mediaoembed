<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Tests\Unit\Service;

use Sto\Mediaoembed\Service\UrlService;
use Sto\Mediaoembed\Tests\Unit\AbstractUnitTest;

final class UrlServiceTest extends AbstractUnitTest
{
    /**
     * @var UrlService
     */
    private $urlService;

    protected function setUp(): void
    {
        $this->urlService = new UrlService();
    }

    public function buildUrlReturnsExpectedStringDataProvider(): array
    {
        return [
            [''],
            ['foo'],
            ['https://www.google.com/'],
            ['https://u:p@foo:1/path/path?q#frag'],
            ['https://u:p@foo:1/path/path?#'],
            ['ssh://root@host'],
            ['://:@:1/?#'],
            ['https://:@foo:1/path/path?#'],
            ['https://@foo:1/path/path?#'],
        ];
    }

    public function testAddQueryParameters(): void
    {
        $testUrl = 'https://www.intera.de?bla=blubb&arr[test]=1&arr1[]=1';
        $addParameters = [
            'new' => 'neu',
            'arr' => ['test1' => 'test'],
            'arr1' => ['2'],
        ];
        $newUrl = $this->urlService->mergeQueryParameters($testUrl, $addParameters);
        $newUrlParameters = [];
        parse_str(parse_url($newUrl, PHP_URL_QUERY), $newUrlParameters);
        $expectedUrlParameters = [
            'bla' => 'blubb',
            'arr' => [
                'test' => '1',
                'test1' => 'test',
            ],
            'arr1' => ['2'],
            'new' => 'neu',
        ];
        self::assertSame($expectedUrlParameters, $newUrlParameters);
    }

    /**
     * @dataProvider buildUrlReturnsExpectedStringDataProvider
     */
    public function testBuildUrlReturnsExpectedString(string $url): void
    {
        $originalUrlParts = parse_url($url);

        $rebuildUrl = $this->urlService->buildUrl($originalUrlParts, $url);
        $rebuildUrlParts = parse_url($rebuildUrl);

        self::assertSame($url, $rebuildUrl);
        self::assertSame($originalUrlParts, $rebuildUrlParts);
    }

    public function testQueryParamsDefaults(): void
    {
        $queryParams = [
            'test1' => 'test',
            'testarr' => ['test' => '1'],
        ];
        $defaults = [
            'test1' => 'test2',
            'testarr' => ['test1' => '2'],
        ];
        $query = http_build_query($queryParams);
        $newQuery = $this->urlService->queryParamsDefaults($query, $defaults);
        $newParams = [];
        parse_str($newQuery, $newParams);
        $expectedParams = [
            'test1' => 'test',
            'testarr' => [
                'test1' => '2',
                'test' => '1',
            ],
        ];
        self::assertSame($expectedParams, $newParams);
    }

    public function testReplaceSchemeAndHost(): void
    {
        /** @noinspection HttpUrlsUsage */
        $oldUrl = 'http://test@bla.com?blubb=1#test';
        $newUrl = $this->urlService->replaceSchemeAndHost($oldUrl, 'https', 'www.my-new-host.com');
        self::assertSame('https://test@www.my-new-host.com?blubb=1#test', $newUrl);
    }
}
