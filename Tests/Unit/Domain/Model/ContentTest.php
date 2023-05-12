<?php

namespace Sto\Mediaoembed\Tests\Unit\Domain\Model;

use PHPUnit\Framework\TestCase;
use Sto\Mediaoembed\Domain\Model\Content;

class ContentTest extends TestCase
{
    /**
     * @var Content
     */
    private $content;

    public function setUp()
    {
        $this->content = new Content(10, 'https://the.media.url', 12, 43, false, '12:2');
    }

    public function testGetAspectRatio()
    {
        $this->assertEquals('12:2', $this->content->getAspectRatio());
    }

    public function testGetMaxHeight()
    {
        $this->assertEquals(12, $this->content->getMaxHeight());
    }

    public function testGetMaxWidth()
    {
        $this->assertEquals(43, $this->content->getMaxWidth());
    }

    public function testGetUid()
    {
        $this->assertEquals(10, $this->content->getUid());
    }

    public function testGetUrl()
    {
        $this->assertEquals('https://the.media.url', $this->content->getUrl());
    }

    public function testShouldPlayRelated()
    {
        $this->assertEquals(false, $this->content->shouldPlayRelated());
    }
}
