<?php

namespace Sto\Mediaoembed\Tests\Unit\Domain\Repository;

use PHPUnit\Framework\TestCase;
use Sto\Mediaoembed\Domain\Model\Provider;
use Sto\Mediaoembed\Domain\Repository\ProviderRepository;
use Sto\Mediaoembed\Exception\InvalidConfigurationException;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

class ProviderRepositoryTest extends TestCase
{
    public function testCreatesProviderWithEndpoint()
    {
        $provider = $this->callFindAll(
            [
                'test' => [
                    'endpoint' => 'https://my-provider.tld/enpoint',
                    'urlSchemes' => ['https://my-url-scheme.tld'],
                ],
            ]
        );

        $this->assertEquals('https://my-provider.tld/enpoint', $provider->getEndpoint());
    }

    public function testCreatesProviderWithHiddenDirectLink()
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

        $this->assertEquals(false, $provider->shouldDirectLinkBeDisplayed());
    }

    public function testCreatesProviderWithProcessors()
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

        $this->assertEquals(['my processor class'], $provider->getProcessors());
    }

    public function testCreatesProviderWithRequestHandler()
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

        $this->assertEquals('theclass', $provider->getRequestHandlerClass());
        $this->assertEquals(['my' => 'setting'], $provider->getRequestHandlerSettings());
    }

    public function testCreatesProviderWithRequestHandlerClass()
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

        $this->assertEquals('My\\Request\\Handler', $provider->getRequestHandlerClass());
    }

    public function testCreatesProviderWithUrlRegex()
    {
        $provider = $this->callFindAll(
            [
                'test' => [
                    'endpoint' => 'https://my-provider.tld/enpoint',
                    'urlRegexes' => ['https://my-url-regex.tld'],
                ],
            ]
        );

        $this->assertTrue($provider->hasRegexUrlSchemes());
        $this->assertEquals(['https://my-url-regex.tld'], $provider->getUrlSchemes());
    }

    public function testCreatesProviderWithUrlScheme()
    {
        $provider = $this->callFindAll(
            [
                'test' => [
                    'endpoint' => 'https://my-provider.tld/enpoint',
                    'urlSchemes' => ['https://my-url-scheme.tld'],
                ],
            ]
        );

        $this->assertFalse($provider->hasRegexUrlSchemes());
        $this->assertEquals(['https://my-url-scheme.tld'], $provider->getUrlSchemes());
    }

    public function testEndpointMustBeAValidUrl()
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Endpoint of provider test is an invalid URL: an invalid URL');

        $this->callFindAll(['test' => ['endpoint' => 'an invalid URL']]);
    }

    public function testEndpointMustNotBeEmpty()
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Endpoint of provider test is empty.');

        $this->callFindAll(['test' => []]);
    }

    public function testMustHaveUrlSchemesOrUrlRegexes()
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('The provider test has no URL schemes.');

        $this->callFindAll(['test' => ['endpoint' => 'https://my-provider.tld/enpoint']]);
    }

    public function testMustNotHaveUrlSchemesAndRegexesAtTheSameTime()
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

    public function testProviderNameMustNotBeEmpty()
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Provider name must not be empty.');

        $this->callFindAll(['' => []]);
    }

    private function callFindAll(array $settings): Provider
    {
        $configurationManagerProphecy = $this->prophesize(ConfigurationManagerInterface::class);
        $configurationManagerProphecy->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS)
            ->shouldBeCalledOnce()
            ->willReturn(['providers' => $settings]);

        $providerRepository = new ProviderRepository($configurationManagerProphecy->reveal());
        $providers = $providerRepository->findAll();

        return $providers[0];
    }
}
