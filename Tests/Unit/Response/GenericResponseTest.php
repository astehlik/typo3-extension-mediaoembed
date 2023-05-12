<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Tests\Unit\Response;

use PHPUnit\Framework\TestCase;
use Sto\Mediaoembed\Response\GenericResponse;

class GenericResponseTest extends TestCase
{
    public function testGetAuthorName(): void
    {
        $response = $this->createResponse(['author_name' => 'author name']);
        self::assertSame('author name', $response->getAuthorName());
    }

    public function testGetAuthorUrl(): void
    {
        $response = $this->createResponse(['author_url' => 'author URL']);
        self::assertSame('author URL', $response->getAuthorUrl());
    }

    public function testGetCacheAge(): void
    {
        $response = $this->createResponse(['cache_age' => 4564765]);
        self::assertSame(4564765, $response->getCacheAge());
    }

    public function testGetProviderName(): void
    {
        $response = $this->createResponse(['provider_name' => 'The name of the provider']);
        self::assertSame('The name of the provider', $response->getProviderName());
    }

    public function testGetProviderUrl(): void
    {
        $response = $this->createResponse(['provider_url' => 'https://the-provider.tld']);
        self::assertSame('https://the-provider.tld', $response->getProviderUrl());
    }

    public function testGetThumbnailHeight(): void
    {
        $response = $this->createResponse(['thumbnail_height' => 100]);
        self::assertSame(100, $response->getThumbnailHeight());
    }

    public function testGetThumbnailUrl(): void
    {
        $response = $this->createResponse(['thumbnail_url' => 'https://the-providerl.tld/the/thumb']);
        self::assertSame('https://the-providerl.tld/the/thumb', $response->getThumbnailUrl());
    }

    public function testGetThumbnailWidth(): void
    {
        $response = $this->createResponse(['thumbnail_width' => 60]);
        self::assertSame(60, $response->getThumbnailWidth());
    }

    public function testGetTitle(): void
    {
        $response = $this->createResponse(['title' => 'the title']);
        self::assertSame('the title', $response->getTitle());
    }

    public function testGetType(): void
    {
        $response = $this->createResponse(['type' => 'generic']);
        self::assertSame('generic', $response->getType());
    }

    public function testGetTypePartialName(): void
    {
        $response = $this->createResponse(['type' => 'generic']);
        self::assertSame('Generic', $response->getTypePartialName());
    }

    public function testGetValueFromResponseDataDefaultsToNull(): void
    {
        $response = $this->createResponse([]);
        self::assertNull($response->getAuthorName());
    }

    public function testGetVersion(): void
    {
        $response = $this->createResponse(['version' => '1.0']);
        self::assertSame('1.0', $response->getVersion());
    }

    protected function createResponse(array $responseData): GenericResponse
    {
        $response = new GenericResponse();
        $response->initializeResponseData($responseData);
        return $response;
    }
}
