<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Request\RequestHandler;

use Sto\Mediaoembed\Content\Configuration;
use Sto\Mediaoembed\Domain\Model\Provider;

interface RequestHandlerInterface
{
    public function handle(Provider $provider, Configuration $configuration): array;
}
