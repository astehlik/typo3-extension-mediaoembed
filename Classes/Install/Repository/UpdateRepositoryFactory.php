<?php
declare(strict_types=1);

namespace Sto\Mediaoembed\Install\Repository;

class UpdateRepositoryFactory
{
    public static function getUpdateRepository()
    {
        if (class_exists('TYPO3\\CMS\\Core\\Database\\Connection')) {
            return new DoctrineUpdateRepository();
        }

        return new DatabaseUpdateRepository();
    }
}
