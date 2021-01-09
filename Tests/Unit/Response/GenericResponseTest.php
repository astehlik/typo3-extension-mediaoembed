<?php

namespace Sto\Mediaoembed\Tests\Unit\Response;

use PHPUnit\Framework\TestCase;
use Sto\Mediaoembed\Response\GenericResponse;

class GenericResponseTest extends TestCase
{
    public function testGetAuthorName()
    {
        $response = $this->createResponse(['author_name' => 'author name']);
        $this->assertEquals('author name', $response->getAuthorName());
    }

    public function testGetAuthorUrl()
    {
        $response = $this->createResponse(['author_url' => 'author URL']);
        $this->assertEquals('author URL', $response->getAuthorUrl());
    }

    public function testGetCacheAge()
    {
        $response = $this->createResponse(['cache_age' => 4564765]);
        $this->assertEquals(4564765, $response->getCacheAge());
    }

    public function testGetProviderName()
    {
        $response = $this->createResponse(['provider_name' => 'The name of the provider']);
        $this->assertEquals('The name of the provider', $response->getProviderName());
    }

    public function testGetProviderUrl()
    {
        $response = $this->createResponse(['provider_url' => 'https://the-provider.tld']);
        $this->assertEquals('https://the-provider.tld', $response->getProviderUrl());
    }

    public function testGetThumbnailHeight()
    {
        $response = $this->createResponse(['thumbnail_height' => 100]);
        $this->assertEquals(100, $response->getThumbnailHeight());
    }

    public function testGetThumbnailUrl()
    {
        $response = $this->createResponse(['thumbnail_url' => 'https://the-providerl.tld/the/thumb']);
        $this->assertEquals('https://the-providerl.tld/the/thumb', $response->getThumbnailUrl());
    }

    public function testGetThumbnailWidth()
    {
        $response = $this->createResponse(['thumbnail_width' => 60]);
        $this->assertEquals(60, $response->getThumbnailWidth());
    }

    public function testGetTitle()
    {
        $response = $this->createResponse(['title' => 'the title']);
        $this->assertEquals('the title', $response->getTitle());
    }

    public function testGetType()
    {
        $response = $this->createResponse(['type' => 'generic']);
        $this->assertEquals('generic', $response->getType());
    }

    public function testGetTypePartialName()
    {
        $response = $this->createResponse(['type' => 'generic']);
        $this->assertEquals('Generic', $response->getTypePartialName());
    }

    public function testGetValueFromResponseDataDefaultsToNull()
    {
        $response = $this->createResponse([]);
        $this->assertNull($response->getAuthorName());
    }

    public function testGetVersion()
    {
        $response = $this->createResponse(['version' => '1.0']);
        $this->assertEquals('1.0', $response->getVersion());
    }

    protected function createResponse(array $responseData): GenericResponse
    {
        $response = new GenericResponse();
        $response->initializeResponseData($responseData);
        return $response;
    }
}
