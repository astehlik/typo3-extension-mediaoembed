<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Tests\Unit\Request;

use PHPUnit\Framework\TestCase;
use Sto\Mediaoembed\Content\Configuration;
use Sto\Mediaoembed\Exception\HttpClientRequestException;
use Sto\Mediaoembed\Exception\HttpNotFoundException;
use Sto\Mediaoembed\Exception\HttpNotImplementedException;
use Sto\Mediaoembed\Exception\HttpUnauthorizedException;
use Sto\Mediaoembed\Request\HttpClient\HttpClientFactory;
use Sto\Mediaoembed\Request\HttpClient\HttpClientInterface;
use Sto\Mediaoembed\Request\HttpRequest;

class HttpRequestTest extends TestCase
{
    public function testAddsMaxHeightToRequestUrl(): void
    {
        $this->assertUrlIs(
            'maxheight=100&format=json&url=http%3A%2F%2Fmy-media.tld%2Ftheurl',
            0,
            100
        );
    }

    public function testAddsMaxWidthAndMaxHeightToRequestUrl(): void
    {
        $this->assertUrlIs(
            'maxwidth=55&maxheight=100&format=json&url=http%3A%2F%2Fmy-media.tld%2Ftheurl',
            55,
            100
        );
    }

    public function testAddsMaxWidthToRequestUrl(): void
    {
        $this->assertUrlIs(
            'maxwidth=100&format=json&url=http%3A%2F%2Fmy-media.tld%2Ftheurl',
            100
        );
    }

    public function testAddsQueryParametersToEndpointWithExistingQueryString(): void
    {
        $this->assertUrlIs(
            'some=get&para=meter&format=json&url=http%3A%2F%2Fmy-media.tld%2Ftheurl',
            0,
            0,
            'https://the-provider.tld/endpoint?some=get&para=meter'
        );
    }

    public function testError401(): void
    {
        $this->expectException(HttpUnauthorizedException::class);
        $this->sendWithException(401);
    }

    public function testError404(): void
    {
        $this->expectException(HttpNotFoundException::class);
        $this->sendWithException(404);
    }

    public function testError501(): void
    {
        $this->expectException(HttpNotImplementedException::class);
        $this->sendWithException(501);
    }

    public function testErrorUnknown(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->sendWithException(500);
    }

    public function testReplacesFormatPlaceholderInUrl(): void
    {
        $this->assertUrlIs(
            'some=get&format=json&someother=param&url=http%3A%2F%2Fmy-media.tld%2Ftheurl',
            0,
            0,
            'https://the-provider.tld/###FORMAT###?some=get&format={format}&someother=param',
            'https://the-provider.tld/json'
        );
    }

    public function testReturnsResponse(): void
    {
        $response = $this->assertUrlIs(
            'maxwidth=100&format=json&url=http%3A%2F%2Fmy-media.tld%2Ftheurl',
            100
        );
        self::assertSame('the repsonse', $response);
    }

    private function assertUrlIs(
        string $expectedQueryString,
        int $maxWidth = 0,
        int $maxHeight = 0,
        string $endpointUrl = 'https://the-provider.tld/endpoint',
        string $expectedBaseUrl = 'https://the-provider.tld/endpoint'
    ): string {
        $expectedUrl = $expectedBaseUrl . '?' . $expectedQueryString;

        $httpClientMock = $this->createMock(HttpClientInterface::class);
        $httpClientMock->expects(self::once())->method('executeGetRequest')
            ->with($expectedUrl)
            ->willReturn('the repsonse');

        $httpRequest = $this->createHttpRequest($maxHeight, $maxWidth, $endpointUrl, $httpClientMock);

        return $httpRequest->sendAndGetResponseData();
    }

    private function createHttpRequest(
        int $maxHeight = 0,
        int $maxWidth = 0,
        string $endpointUrl = 'https://the-provider.tld/endpoint',
        ?HttpClientInterface $httpClient = null
    ): HttpRequest {
        $configurationMock = $this->createMock(Configuration::class);
        $configurationMock->expects(self::once())->method('getMaxheight')->willReturn($maxHeight);
        $configurationMock->expects(self::once())->method('getMaxwidth')->willReturn($maxWidth);
        $configurationMock->method('getMediaUrl')->willReturn('http://my-media.tld/theurl');

        $httpClientFactoryMock = $this->createMock(HttpClientFactory::class);
        $httpClientFactoryMock->expects(self::once())
            ->method('getHttpClient')
            ->willReturn($httpClient ?? $this->createMock(HttpClientInterface::class));

        return new HttpRequest($configurationMock, $endpointUrl, $httpClientFactoryMock);
    }

    private function sendWithException(int $errorCode): void
    {
        $httpClientMock = $this->createMock(HttpClientInterface::class);
        $httpClientMock->method('executeGetRequest')->willThrowException(
            new HttpClientRequestException('an error', $errorCode)
        );

        $httpRequest = $this->createHttpRequest(
            0,
            0,
            'https://the-provider.tld/endpoint',
            $httpClientMock
        );

        $httpRequest->sendAndGetResponseData();
    }
}
