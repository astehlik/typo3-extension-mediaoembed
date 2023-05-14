<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Tests\Unit\Domain\Repository;

use PHPUnit\Framework\TestCase;
use Sto\Mediaoembed\Domain\Model\Provider;
use Sto\Mediaoembed\Domain\Repository\ProviderRepository;
use Sto\Mediaoembed\Exception\InvalidConfigurationException;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

class ProviderRepositoryTest extends TestCase
{
    public function testCreatesProviderWithEndpoint(): void
    {
        $provider = $this->callFindAll(
            [
                'test' => [
                    'endpoint' => 'https://my-provider.tld/enpoint',
                    'urlSchemes' => ['https://my-url-scheme.tld'],
                ],
            ]
        );

        self::assertSame('https://my-provider.tld/enpoint', $provider->getEndpoint());
    }

    public function testCreatesProviderWithHiddenDirectLink(): void
    {
        $provider = $this->callFindAll(
            [
                'test' => [
                    'endpoint' => 'https://my-provider.tld/enpoint',
                    'urlSchemes' => ['https://my-url-scheme.tld'],
                    'displayDirectLink' => '0',
                ],
            ]
        );

        self::assertFalse($provider->shouldDirectLinkBeDisplayed());
    }

    public function testCreatesProviderWithProcessors(): void
    {
        $provider = $this->callFindAll(
            [
                'test' => [
                    'endpoint' => 'https://my-provider.tld/enpoint',
                    'urlSchemes' => ['https://my-url-scheme.tld'],
                    'processors' => ['my processor class'],
                ],
            ]
        );

        self::assertSame(['my processor class'], $provider->getProcessors());
    }

    public function testCreatesProviderWithRequestHandler(): void
    {
        $provider = $this->callFindAll(
            [
                'test' => [
                    'endpoint' => 'https://my-provider.tld/enpoint',
                    'urlSchemes' => ['https://my-url-scheme.tld'],
                    'requestHandlerClass' => 'theclass',
                    'requestHandlerSettings' => ['my' => 'setting'],
                ],
            ]
        );

        self::assertSame('theclass', $provider->getRequestHandlerClass());
        self::assertSame(['my' => 'setting'], $provider->getRequestHandlerSettings());
    }

    public function testCreatesProviderWithRequestHandlerClass(): void
    {
        $provider = $this->callFindAll(
            [
                'test' => [
                    'endpoint' => 'https://my-provider.tld/enpoint',
                    'requestHandlerClass' => 'My\\Request\\Handler',
                    'urlSchemes' => ['https://my-url-scheme.tld'],
                ],
            ]
        );

        self::assertSame('My\\Request\\Handler', $provider->getRequestHandlerClass());
    }

    public function testCreatesProviderWithUrlRegex(): void
    {
        $provider = $this->callFindAll(
            [
                'test' => [
                    'endpoint' => 'https://my-provider.tld/enpoint',
                    'urlRegexes' => ['https://my-url-regex.tld'],
                ],
            ]
        );

        self::assertTrue($provider->hasRegexUrlSchemes());
        self::assertSame(['https://my-url-regex.tld'], $provider->getUrlSchemes());
    }

    public function testCreatesProviderWithUrlScheme(): void
    {
        $provider = $this->callFindAll(
            [
                'test' => [
                    'endpoint' => 'https://my-provider.tld/enpoint',
                    'urlSchemes' => ['https://my-url-scheme.tld'],
                ],
            ]
        );

        self::assertFalse($provider->hasRegexUrlSchemes());
        self::assertSame(['https://my-url-scheme.tld'], $provider->getUrlSchemes());
    }

    public function testEndpointMustBeAValidUrl(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Endpoint of provider test is an invalid URL: an invalid URL');

        $this->callFindAll(['test' => ['endpoint' => 'an invalid URL']]);
    }

    public function testEndpointMustNotBeEmpty(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Endpoint of provider test is empty.');

        $this->callFindAll(['test' => []]);
    }

    public function testMustHaveUrlSchemesOrUrlRegexes(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('The provider test has no URL schemes.');

        $this->callFindAll(['test' => ['endpoint' => 'https://my-provider.tld/enpoint']]);
    }

    public function testMustNotHaveUrlSchemesAndRegexesAtTheSameTime(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage(
            'A provider can have either urlRegexes or urlSchemes. The provider test has both.'
        );

        $this->callFindAll(
            [
                'test' => [
                    'endpoint' => 'https://my-provider.tld/enpoint',
                    'urlSchemes' => ['https://my-url-scheme.tld'],
                    'urlRegexes' => ['https://my-url-regex.tld'],
                ],
            ]
        );
    }

    public function testProviderNameMustNotBeEmpty(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Provider name must not be empty.');

        $this->callFindAll(['' => []]);
    }

    private function callFindAll(array $settings): Provider
    {
        $configurationManagerMock = $this->createMock(ConfigurationManagerInterface::class);
        $configurationManagerMock->expects(self::once())
            ->method('getConfiguration')
            ->with(ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS)
            ->willReturn(['providers' => $settings]);

        $providerRepository = new ProviderRepository($configurationManagerMock);
        $providers = $providerRepository->findAll();

        return $providers[0];
    }
}
