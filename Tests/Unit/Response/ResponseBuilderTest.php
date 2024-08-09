<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Tests\Unit\Response;

use PHPUnit\Framework\MockObject\MockObject;
use Sto\Mediaoembed\Content\Configuration;
use Sto\Mediaoembed\Content\Settings;
use Sto\Mediaoembed\Domain\Model\Content;
use Sto\Mediaoembed\Response\GenericResponse;
use Sto\Mediaoembed\Response\LinkResponse;
use Sto\Mediaoembed\Response\PhotoResponse;
use Sto\Mediaoembed\Response\ResponseBuilder;
use Sto\Mediaoembed\Response\RichResponse;
use Sto\Mediaoembed\Response\VideoResponse;
use Sto\Mediaoembed\Service\AspectRatioCalculatorInterface;
use Sto\Mediaoembed\Service\PhotoDownloadService;
use Sto\Mediaoembed\Tests\Unit\AbstractUnitTestCase;
use TYPO3\CMS\Core\Resource\FileInterface;

class ResponseBuilderTest extends AbstractUnitTestCase
{
    public function testBuildResponseGeneric(): void
    {
        $responseData = [
            'type' => 'a custom response',
            'title' => 'Some generic response',
        ];
        $responseClass = GenericResponse::class;

        $response = $this->buildResponse($responseClass, $responseData);
        self::assertSame('Some generic response', $response->getTitle());
    }

    public function testBuildResponseLink(): void
    {
        $responseData = [
            'type' => 'link',
            'title' => 'My link title',
        ];
        $responseClass = LinkResponse::class;

        /** @var LinkResponse $response */
        $response = $this->buildResponse($responseClass, $responseData);
        self::assertSame('My link title', $response->getTitle());
    }

    public function testBuildResponsePhoto(): void
    {
        $responseData = [
            'type' => 'photo',
            'url' => 'https://my-awsome.tld/photo',
            'width' => 100,
            'height' => 60,
        ];
        $responseClass = PhotoResponse::class;

        $fileMock = $this->createMock(FileInterface::class);

        $configuration = $this->createConfiguration();

        $photoDownloadServiceMock = $this->createMock(PhotoDownloadService::class);
        $photoDownloadServiceMock->expects(self::once())
            ->method('downloadPhoto')
            ->with('https://my-awsome.tld/photo', $configuration)
            ->willReturn($fileMock);

        /** @var PhotoResponse $response */
        $response = $this->buildResponse($responseClass, $responseData, $configuration, $photoDownloadServiceMock);
        self::assertSame('https://my-awsome.tld/photo', $response->getUrl());
        self::assertSame($fileMock, $response->getLocalFile());
    }

    public function testBuildResponseRich(): void
    {
        $responseData = [
            'type' => 'rich',
            'html' => '<div>dummyrich</div>',
            'width' => 100,
            'height' => 60,
        ];
        $responseClass = RichResponse::class;

        /** @var RichResponse $response */
        $response = $this->buildResponse($responseClass, $responseData);
        self::assertSame('<div>dummyrich</div>', $response->getHtml());
    }

    public function testBuildResponseVideo(): void
    {
        $responseData = [
            'type' => 'video',
            'html' => '<div>dummyvideo</div>',
            'width' => 100,
            'height' => 60,
        ];
        $responseClass = VideoResponse::class;

        /** @var VideoResponse $response */
        $response = $this->buildResponse($responseClass, $responseData);
        self::assertSame('<div>dummyvideo</div>', $response->getHtml());
    }

    protected function buildResponse(
        string $responseClass,
        array $responseData,
        ?Configuration $configuration = null,
        null|MockObject|PhotoDownloadService $photoDownloadServiceMock = null,
    ): GenericResponse {
        if (!$photoDownloadServiceMock) {
            $photoDownloadServiceMock = $this->createMock(PhotoDownloadService::class);
        }

        $reponseBuilder = new ResponseBuilder($photoDownloadServiceMock);

        $response = $reponseBuilder->buildResponse(
            $responseData,
            $configuration ?: $this->createConfiguration(),
        );

        self::assertInstanceOf($responseClass, $response);

        return $response;
    }

    private function createConfiguration(): Configuration
    {
        return new Configuration(
            new Content(12, 'https://the-url.tld/embed'),
            new Settings([]),
            $this->createMock(AspectRatioCalculatorInterface::class),
        );
    }
}
