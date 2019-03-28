<?php
declare(strict_types=1);

namespace Sto\Mediaoembed\Install\Repository;

class UpdateRepositoryFactory
{
    /**
     * @return UpdateRepository
     */
    public static function getUpdateRepository(): UpdateRepository
    {
        if (class_exists('TYPO3\\CMS\\Core\\Database\\Connection')) {
            return new DoctrineUpdateRepository();
        }

        return new DatabaseUpdateRepository();
    }
}
