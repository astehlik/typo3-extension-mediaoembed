<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Tests\Unit\Event;

/*                                                                        *
 * This script belongs to the TYPO3 Extension "mediaoembed".              *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use PHPUnit\Framework\Attributes\CoversClass;
use Sto\Mediaoembed\Event\BeforeMediaUrlResolvedEvent;
use Sto\Mediaoembed\Tests\Unit\AbstractUnitTestCase;

#[CoversClass(BeforeMediaUrlResolvedEvent::class)]
final class BeforeMediaUrlResolvedEventTest extends AbstractUnitTestCase
{
    public function testGetUrlReturnsConstructorValue(): void
    {
        $event = new BeforeMediaUrlResolvedEvent('https://example.com/video');
        $this->assertSame('https://example.com/video', $event->getUrl());
    }

    public function testSetUrlOverridesUrl(): void
    {
        $event = new BeforeMediaUrlResolvedEvent('https://example.com/video');
        $event->setUrl('https://example.com/other-video');
        $this->assertSame('https://example.com/other-video', $event->getUrl());
    }
}
