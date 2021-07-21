<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Response;

interface HtmlAwareResponseInterface
{
    public function getHtml(): string;

    public function setHtml(string $html);
}
