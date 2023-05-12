<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Tests\Unit\Request\RequestHandler;

use PHPUnit\Framework\MockObject\MockObject;
use Sto\Mediaoembed\Content\Configuration;
use Sto\Mediaoembed\Domain\Model\Provider;
use Sto\Mediaoembed\Exception\InvalidResponseException;
use Sto\Mediaoembed\Request\HttpClient\HttpClientFactory;
use Sto\Mediaoembed\Request\HttpClient\HttpClientInterface;
use Sto\Mediaoembed\Request\HttpRequest;
use Sto\Mediaoembed\Request\RequestHandler\HttpRequestHandler;
use Sto\Mediaoembed\Tests\Unit\AbstractUnitTest;

final class HttpRequestHandlerTest extends AbstractUnitTest
{
    /**
     * @var MockObject|HttpClientFactory
     */
    private $httpClientFactoryMock;

    private HttpRequestHandler $requestHandler;

    protected function setUp(): void
    {
        $this->httpClientFactoryMock = $this->createMock(HttpClientFactory::class);

        $this->requestHandler = new HttpRequestHandler($this->httpClientFactoryMock);
    }

    public function testInvalidResponseDataThrowsException(): void
    {
        $this->expectException(InvalidResponseException::class);

        $this->callHandler('invalid');
    }

    public function testValidResponseReturnsDecodedJson(): void
    {
        $responseArray = ['my' => 'array'];
        $response = $this->callHandler(json_encode($responseArray));

        self::assertSame($responseArray, $response);
    }

    private function callHandler(string $responseData): array
    {
        $httpRequestMock = $this->createMock(HttpRequest::class);
        $httpRequestMock->method('sendAndGetResponseData')->willReturn($responseData);

        $httpClientMock = $this->createMock(HttpClientInterface::class);
        $httpClientMock->method('executeGetRequest')->willReturn($responseData);

        $this->httpClientFactoryMock->method('getHttpClient')
            ->willReturn($httpClientMock);

        $provider = $this->createMock(Provider::class);
        $provider->method('getEndpoint')->willReturn('https://the-endpoint.org');

        return $this->requestHandler->handle($provider, $this->createMock(Configuration::class));
    }
}
