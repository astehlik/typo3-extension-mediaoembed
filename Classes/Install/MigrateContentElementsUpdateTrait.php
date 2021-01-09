<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Install;

use Sto\Mediaoembed\Install\Repository\UpdateRepositoryFactory;

trait MigrateContentElementsUpdateTrait
{
    /**
     * @var FlexFormUpdateHandler
     */
    private $flexFormUpdateHandler;

    private function getFlexFormUpdateHandler()
    {
        if ($this->flexFormUpdateHandler) {
            return $this->flexFormUpdateHandler;
        }

        $updateRepository = UpdateRepositoryFactory::getUpdateRepository();
        $this->flexFormUpdateHandler = new FlexFormUpdateHandler($updateRepository);
        return $this->flexFormUpdateHandler;
    }
}
