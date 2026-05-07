<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Tests\Unit\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Sto\Mediaoembed\Service\HttpService;
use Sto\Mediaoembed\Tests\Unit\AbstractUnitTestCase;

#[CoversClass(HttpService::class)]
final class HttpServiceTest extends AbstractUnitTestCase
{
    public function testGetUrlExecutesGetRequest(): void
    {
        $requestMock = $this->createMock(RequestInterface::class);
        $responseMock = $this->createMock(ResponseInterface::class);

        $requestFactoryMock = $this->createMock(RequestFactoryInterface::class);
        $requestFactoryMock
            ->expects($this->once())
            ->method('createRequest')
            ->with('GET', 'https://example.com')
            ->willReturn($requestMock);

        $clientMock = $this->createMock(ClientInterface::class);
        $clientMock
            ->expects($this->once())
            ->method('sendRequest')
            ->with($requestMock)
            ->willReturn($responseMock);

        $httpService = new HttpService($requestFactoryMock, $clientMock);
        // @extensionScannerIgnoreLine - False positive
        $response = $httpService->getUrl('https://example.com');

        $this->assertSame($responseMock, $response);
    }
}
