<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Tests\Unit\Request\RequestHandler;

use PHPUnit\Framework\MockObject\MockObject;
use Sto\Mediaoembed\Content\Configuration;
use Sto\Mediaoembed\Domain\Model\Provider;
use Sto\Mediaoembed\Exception\InvalidResponseException;
use Sto\Mediaoembed\Request\HttpRequest;
use Sto\Mediaoembed\Request\RequestHandler\HttpRequestHandler;
use Sto\Mediaoembed\Tests\Unit\AbstractUnitTest;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;

final class HttpRequestHandlerTest extends AbstractUnitTest
{
    /**
     * @var MockObject|Configuration
     */
    private $configurationMock;

    /**
     * @var MockObject|ObjectManagerInterface
     */
    private $objectManagerMock;

    /**
     * @var HttpRequestHandler
     */
    private $requestHandler;

    public function setUp()
    {
        $this->configurationMock = $this->createMock(Configuration::class);
        $this->objectManagerMock = $this->createMock(ObjectManagerInterface::class);

        $this->requestHandler = new HttpRequestHandler($this->configurationMock, $this->objectManagerMock);
    }

    public function testInvalidResponseDataThrowsException()
    {
        $this->expectException(InvalidResponseException::class);

        $this->callHandler('invalid');
    }

    public function testValidResponseReturnsDecodedJson()
    {
        $responseArray = ['my' => 'array'];
        $response = $this->callHandler(json_encode($responseArray));

        $this->assertEquals($responseArray, $response);
    }

    private function callHandler(string $responseData): array
    {
        $httpRequestMock = $this->createMock(HttpRequest::class);
        $httpRequestMock->method('sendAndGetResponseData')->willReturn($responseData);

        /** @noinspection PhpParamsInspection */
        $this->objectManagerMock->method('get')
            ->with(HttpRequest::class, $this->configurationMock, 'https://the-endpoint.org')
            ->willReturn($httpRequestMock);

        $provider = $this->createMock(Provider::class);
        $provider->method('getEndpoint')->willReturn('https://the-endpoint.org');

        return $this->requestHandler->handle($provider);
    }
}
