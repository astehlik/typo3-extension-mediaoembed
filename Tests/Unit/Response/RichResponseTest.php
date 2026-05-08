<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Tests\Unit\Response;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Sto\Mediaoembed\Content\Configuration;
use Sto\Mediaoembed\Response\RichResponse;

#[CoversClass(RichResponse::class)]
class RichResponseTest extends TestCase
{
    public function testRichResponseAccessors(): void
    {
        $data = [
            'html' => '<div>Rich content</div>',
            'width' => 600,
            'height' => 400,
            'type' => 'rich',
            'version' => '1.0',
        ];

        $response = new RichResponse();
        $response->initializeResponseData($data, $this->createMock(Configuration::class));

        $this->assertSame('<div>Rich content</div>', $response->getHtml());
        $this->assertSame(600, $response->getWidth());
        $this->assertSame(400, $response->getHeight());
        $this->assertSame(1.5, $response->getAspectRatio());

        $response->setHtml('<span>New content</span>');
        $this->assertSame('<span>New content</span>', $response->getHtml());
    }
}
