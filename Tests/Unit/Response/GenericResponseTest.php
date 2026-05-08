<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Tests\Unit\Response;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Sto\Mediaoembed\Content\Configuration;
use Sto\Mediaoembed\Response\GenericResponse;

#[CoversClass(GenericResponse::class)]
class GenericResponseTest extends TestCase
{
    public function testGetAuthorName(): void
    {
        $response = $this->createResponse(['author_name' => 'author name']);
        $this->assertSame('author name', $response->getAuthorName());
    }

    public function testGetAuthorUrl(): void
    {
        $response = $this->createResponse(['author_url' => 'author URL']);
        $this->assertSame('author URL', $response->getAuthorUrl());
    }

    public function testGetCacheAge(): void
    {
        $response = $this->createResponse(['cache_age' => 4564765]);
        $this->assertSame(4564765, $response->getCacheAge());
    }

    public function testGetConfiguration(): void
    {
        $configurationMock = $this->createMock(Configuration::class);
        $response = new GenericResponse();
        $response->initializeResponseData([], $configurationMock);
        $this->assertSame($configurationMock, $response->getConfiguration());
    }

    public function testGetProviderName(): void
    {
        $response = $this->createResponse(['provider_name' => 'The name of the provider']);
        $this->assertSame('The name of the provider', $response->getProviderName());
    }

    public function testGetProviderUrl(): void
    {
        $response = $this->createResponse(['provider_url' => 'https://the-provider.tld']);
        $this->assertSame('https://the-provider.tld', $response->getProviderUrl());
    }

    public function testGetResponseDataArray(): void
    {
        $data = [
            'type' => 'test',
            'version' => '1.0',
        ];
        $response = $this->createResponse($data);
        $this->assertSame($data, $response->getResponseDataArray());
    }

    public function testGetThumbnailHeight(): void
    {
        $response = $this->createResponse(['thumbnail_height' => 100]);
        $this->assertSame(100, $response->getThumbnailHeight());
    }

    public function testGetThumbnailUrl(): void
    {
        $response = $this->createResponse(['thumbnail_url' => 'https://the-providerl.tld/the/thumb']);
        $this->assertSame('https://the-providerl.tld/the/thumb', $response->getThumbnailUrl());
    }

    public function testGetThumbnailWidth(): void
    {
        $response = $this->createResponse(['thumbnail_width' => 60]);
        $this->assertSame(60, $response->getThumbnailWidth());
    }

    public function testGetTitle(): void
    {
        $response = $this->createResponse(['title' => 'the title']);
        $this->assertSame('the title', $response->getTitle());
    }

    public function testGetType(): void
    {
        $response = $this->createResponse(['type' => 'generic']);
        $this->assertSame('generic', $response->getType());
    }

    public function testGetTypePartialName(): void
    {
        $response = $this->createResponse(['type' => 'generic']);
        $this->assertSame('Generic', $response->getTypePartialName());
    }

    public function testGetValueFromResponseDataDefaultsToEmptyString(): void
    {
        $response = $this->createResponse([]);
        $this->assertSame('', $response->getAuthorName());
    }

    public function testGetVersion(): void
    {
        $response = $this->createResponse(['version' => '1.0']);
        $this->assertSame('1.0', $response->getVersion());
    }

    protected function createResponse(array $responseData): GenericResponse
    {
        $response = new GenericResponse();
        $response->initializeResponseData($responseData, $this->createMock(Configuration::class));
        return $response;
    }
}
