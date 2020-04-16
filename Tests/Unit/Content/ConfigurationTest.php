<?php

namespace Sto\Mediaoembed\Tests\Unit\Content;

use PHPUnit\Framework\TestCase;
use Sto\Mediaoembed\Content\Configuration;
use Sto\Mediaoembed\Domain\Model\Content;
use Sto\Mediaoembed\Domain\Repository\ContentRepository;
use Sto\Mediaoembed\Service\ConfigurationService;

class ConfigurationTest extends TestCase
{
    private $configurationServiceProphecy;

    private $contentProphecy;

    private $contentRepositoryProphecy;

    public function setUp()
    {
        $this->contentProphecy = $this->prophesize(Content::class);
        $this->contentRepositoryProphecy = $this->prophesize(ContentRepository::class);
        $this->contentRepositoryProphecy->getCurrentContent()->willReturn($this->contentProphecy->reveal());

        $this->configurationServiceProphecy = $this->prophesize(ConfigurationService::class);
    }

    public function getMaxWidthHeightDataProvider()
    {
        return [
            'settings and content object zero returns zero' => [
                0,
                0,
                0,
            ],
            'settings zero, content object set uses content object' => [
                10,
                0,
                10,
            ],
            'settings set, content object zero uses settings' => [
                0,
                20,
                20,
            ],
            'settings set, content object set uses content object' => [
                30,
                20,
                30,
            ],
        ];
    }

    /**
     * @param int $contentValue
     * @param int $settingsValue
     * @param int $expectedValue
     * @dataProvider getMaxWidthHeightDataProvider
     */
    public function testGetMaxheight(int $contentValue, int $settingsValue, int $expectedValue)
    {
        $this->contentProphecy->getMaxHeight()->willReturn($contentValue);
        $this->configurationServiceProphecy->getMaxHeight()->willReturn($settingsValue);

        $this->assertEquals($expectedValue, $this->getConfiguration()->getMaxheight());
    }

    /**
     * @param int $contentValue
     * @param int $settingsValue
     * @param int $expectedValue
     * @dataProvider getMaxWidthHeightDataProvider
     */
    public function testGetMaxwidth(int $contentValue, int $settingsValue, int $expectedValue)
    {
        $this->contentProphecy->getMaxWidth()->willReturn($contentValue);
        $this->configurationServiceProphecy->getMaxWidth()->willReturn($settingsValue);

        $this->assertEquals($expectedValue, $this->getConfiguration()->getMaxwidth());
    }

    public function testGetMediaUrlReturnsUrlFromContent()
    {
        $this->contentProphecy->getUrl()->willReturn('http://my.test.url');

        $this->assertEquals('http://my.test.url', $this->getConfiguration()->getMediaUrl());
    }

    protected function getConfiguration(): Configuration
    {
        return new Configuration(
            $this->configurationServiceProphecy->reveal(),
            $this->contentRepositoryProphecy->reveal()
        );
    }
}
