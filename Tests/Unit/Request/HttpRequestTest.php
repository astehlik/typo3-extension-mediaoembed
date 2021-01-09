<?php

namespace Sto\Mediaoembed\Tests\Unit\Request;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use RuntimeException;
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
    public function testAddsMaxHeightToRequestUrl()
    {
        $this->assertUrlIs(
            'maxheight=100&format=json&url=http%3A%2F%2Fmy-media.tld%2Ftheurl',
            0,
            100
        );
    }

    public function testAddsMaxWidthAndMaxHeightToRequestUrl()
    {
        $this->assertUrlIs(
            'maxwidth=55&maxheight=100&format=json&url=http%3A%2F%2Fmy-media.tld%2Ftheurl',
            55,
            100
        );
    }

    public function testAddsMaxWidthToRequestUrl()
    {
        $this->assertUrlIs(
            'maxwidth=100&format=json&url=http%3A%2F%2Fmy-media.tld%2Ftheurl',
            100,
            0
        );
    }

    public function testAddsQueryParametersToEndpointWithExistingQueryString()
    {
        $this->assertUrlIs(
            'some=get&para=meter&format=json&url=http%3A%2F%2Fmy-media.tld%2Ftheurl',
            0,
            0,
            'https://the-provider.tld/endpoint?some=get&para=meter'
        );
    }

    public function testError401()
    {
        $this->expectException(HttpUnauthorizedException::class);
        $this->sendWithException(401);
    }

    public function testError404()
    {
        $this->expectException(HttpNotFoundException::class);
        $this->sendWithException(404);
    }

    public function testError501()
    {
        $this->expectException(HttpNotImplementedException::class);
        $this->sendWithException(501);
    }

    public function testErrorUnknown()
    {
        $this->expectException(RuntimeException::class);
        $this->sendWithException(500);
    }

    public function testReplacesFormatPlaceholderInUrl()
    {
        $this->assertUrlIs(
            'some=get&format=json&someother=param&url=http%3A%2F%2Fmy-media.tld%2Ftheurl',
            0,
            0,
            'https://the-provider.tld/###FORMAT###?some=get&format={format}&someother=param',
            'https://the-provider.tld/json'
        );
    }

    public function testReturnsResponse()
    {
        $response = $this->assertUrlIs(
            'maxwidth=100&format=json&url=http%3A%2F%2Fmy-media.tld%2Ftheurl',
            100,
            0
        );
        $this->assertEquals('the repsonse', $response);
    }

    private function assertUrlIs(
        string $expectedQueryString,
        int $maxWidth = 0,
        int $maxHeight = 0,
        string $endpointUrl = 'https://the-provider.tld/endpoint',
        string $expectedBaseUrl = 'https://the-provider.tld/endpoint'
    ): string {
        $httpRequest = $this->createHttpRequest($maxHeight, $maxWidth, $endpointUrl);

        $expectedUrl = $expectedBaseUrl . '?' . $expectedQueryString;
        $httpClientPromise = $this->prophesize(HttpClientInterface::class);
        $httpClientPromise->executeGetRequest(Argument::any())->willReturn('the repsonse');
        $httpClientPromise->executeGetRequest($expectedUrl)->shouldBeCalledOnce()->willReturn('the repsonse');

        $this->injectHttpClientFactory($httpRequest, $httpClientPromise->reveal());

        return $httpRequest->sendAndGetResponseData();
    }

    private function createHttpRequest(
        int $maxHeight = 0,
        int $maxWidth = 0,
        string $endpointUrl = 'https://the-provider.tld/endpoint'
    ): HttpRequest {
        $configurationProphecy = $this->prophesize(Configuration::class);
        $configurationProphecy->getMaxheight()->shouldBeCalledOnce()->willReturn($maxHeight);
        $configurationProphecy->getMaxwidth()->shouldBeCalledOnce()->willReturn($maxWidth);
        $configurationProphecy->getMediaUrl()->shouldBeCalled()->willReturn('http://my-media.tld/theurl');

        return new HttpRequest($configurationProphecy->reveal(), $endpointUrl);
    }

    private function injectHttpClientFactory(HttpRequest $httpRequest, HttpClientInterface $httpClient)
    {
        $httpClientFactoryProphecy = $this->prophesize(HttpClientFactory::class);
        $httpClientFactoryProphecy->getHttpClient()->shouldBeCalledOnce()->willReturn($httpClient);
        $httpRequest->injectHttpClientFactory($httpClientFactoryProphecy->reveal());
    }

    private function sendWithException(int $errorCode)
    {
        $httpRequest = $this->createHttpRequest();

        $httpClientPromise = $this->prophesize(HttpClientInterface::class);
        $httpClientPromise->executeGetRequest(Argument::any())->willThrow(
            new HttpClientRequestException('an error', $errorCode)
        );

        $this->injectHttpClientFactory($httpRequest, $httpClientPromise->reveal());

        $httpRequest->sendAndGetResponseData();
    }
}
