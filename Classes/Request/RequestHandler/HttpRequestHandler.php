<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Request\RequestHandler;

use Sto\Mediaoembed\Content\Configuration;
use Sto\Mediaoembed\Domain\Model\Provider;
use Sto\Mediaoembed\Exception\InvalidResponseException;
use Sto\Mediaoembed\Request\HttpRequest;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;

final class HttpRequestHandler implements RequestHandlerInterface
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    public function __construct(Configuration $configuration, ObjectManagerInterface $objectManager)
    {
        $this->configuration = $configuration;
        $this->objectManager = $objectManager;
    }

    public function handle(Provider $provider): array
    {
        /** @var HttpRequest $request */
        /** @noinspection PhpParamsInspection */
        $request = $this->objectManager->get(HttpRequest::class, $this->configuration, $provider->getEndpoint());
        $responseData = $request->sendAndGetResponseData();

        return $this->parseResponseData($responseData);
    }

    private function parseResponseData(string $responseData): array
    {
        $parsedResponseData = json_decode($responseData, true);

        if ($parsedResponseData === null) {
            throw new InvalidResponseException($responseData);
        }

        return $parsedResponseData;
    }
}
