<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Tests\Unit\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Sto\Mediaoembed\Exception\InvalidUrlException;
use Sto\Mediaoembed\Service\UrlService;
use Sto\Mediaoembed\Tests\Unit\AbstractUnitTestCase;

#[CoversClass(UrlService::class)]
final class UrlServiceTest extends AbstractUnitTestCase
{
    private UrlService $urlService;

    protected function setUp(): void
    {
        $this->urlService = new UrlService();
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
        $this->assertSame($expectedUrlParameters, $newUrlParameters);
    }

    #[DataProvider('provideBuildUrlReturnsExpectedStringCases')]
    public function testBuildUrlReturnsExpectedString(string $url): void
    {
        $originalUrlParts = parse_url($url);

        $rebuildUrl = $this->urlService->buildUrl($originalUrlParts, $url);
        $rebuildUrlParts = parse_url($rebuildUrl);

        $this->assertSame($url, $rebuildUrl);
        $this->assertSame($originalUrlParts, $rebuildUrlParts);
    }

    public static function provideBuildUrlReturnsExpectedStringCases(): iterable
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

    public function testMergeQueryParametersWithEmptyParameters(): void
    {
        $url = 'https://example.com/path?existing=value';
        $result = $this->urlService->mergeQueryParameters($url, []);
        $this->assertSame($url, $result);
    }

    #[DataProvider('provideParseUrlReturnsExpectedArrayCases')]
    public function testParseUrlReturnsExpectedArray(string $url, array $expected): void
    {
        $result = $this->urlService->parseUrl($url);
        $this->assertSame($expected, $result);
    }

    public static function provideParseUrlReturnsExpectedArrayCases(): iterable
    {
        return [
            'simple url' => [
                'https://example.com/path',
                [
                    'scheme' => 'https',
                    'host' => 'example.com',
                    'path' => '/path',
                ],
            ],
            'url with query' => [
                'https://example.com?key=value',
                [
                    'scheme' => 'https',
                    'host' => 'example.com',
                    'query' => 'key=value',
                ],
            ],
            'url with fragment' => [
                'https://example.com#section',
                [
                    'scheme' => 'https',
                    'host' => 'example.com',
                    'fragment' => 'section',
                ],
            ],
            'url with port' => [
                'https://example.com:8080/path',
                [
                    'scheme' => 'https',
                    'host' => 'example.com',
                    'port' => 8080,
                    'path' => '/path',
                ],
            ],
        ];
    }

    public function testParseUrlThrowsExceptionForInvalidUrl(): void
    {
        $url = 'http://:80';

        try {
            $this->urlService->parseUrl($url);
            $this->fail('Exception not thrown');
        } catch (InvalidUrlException $e) {
            $this->assertSame($url, $e->getUrl());
        }
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
        $this->assertSame($expectedParams, $newParams);
    }

    public function testQueryParamsDefaultsWithEmptyDefaults(): void
    {
        $queryParams = ['key' => 'value'];
        $query = http_build_query($queryParams);
        $newQuery = $this->urlService->queryParamsDefaults($query, []);
        $this->assertSame($query, $newQuery);
    }

    public function testQueryParamsOverwrite(): void
    {
        $query = 'key1=value1&key2=value2';
        $overwrite = [
            'key2' => 'newvalue',
            'key3' => 'value3',
        ];
        $result = $this->urlService->queryParamsOverwrite($query, $overwrite);
        $parsed = [];
        parse_str($result, $parsed);
        $this->assertSame(['key1' => 'value1', 'key2' => 'newvalue', 'key3' => 'value3'], $parsed);
    }

    public function testQueryParamsOverwriteWithEmptyOverwrite(): void
    {
        $query = 'key=value';
        $result = $this->urlService->queryParamsOverwrite($query, []);
        $this->assertSame($query, $result);
    }

    public function testReplaceSchemeAndHost(): void
    {
        /** @noinspection HttpUrlsUsage */
        $oldUrl = 'http://test@bla.com?blubb=1#test';
        $newUrl = $this->urlService->replaceSchemeAndHost($oldUrl, 'https', 'www.my-new-host.com');
        $this->assertSame('https://test@www.my-new-host.com?blubb=1#test', $newUrl);
    }

    #[DataProvider('provideReplaceSchemeAndHostWithVariousUrlsCases')]
    public function testReplaceSchemeAndHostWithVariousUrls(
        string $url,
        string $newScheme,
        string $newHost,
        string $expected
    ): void {
        $result = $this->urlService->replaceSchemeAndHost($url, $newScheme, $newHost);
        $this->assertSame($expected, $result);
    }

    public static function provideReplaceSchemeAndHostWithVariousUrlsCases(): iterable
    {
        return [
            'http to https' => [
                'http://example.com/path',
                'https',
                'example.com',
                'https://example.com/path',
            ],
            'change host' => [
                'https://old.com/path',
                'https',
                'new.com',
                'https://new.com/path',
            ],
            // Note: replaceSchemeAndHost doesn't remove port, it's replaced with new host
            'change both' => [
                'http://old.com:8080/path?query=1',
                'https',
                'new.com',
                'https://new.com:8080/path?query=1',
            ],
        ];
    }
}
