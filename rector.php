<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\LogicalAnd\LogicalToBooleanRector;
use Rector\Config\RectorConfig;
use Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector;
use Rector\PostRector\Rector\NameImportingPostRector;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;
use Rector\TypeDeclaration\Rector\ClassMethod\AddVoidReturnTypeWhereNoReturnRector;
use Rector\ValueObject\PhpVersion;
use Ssch\TYPO3Rector\CodeQuality\General\AddErrorCodeToExceptionRector;
use Ssch\TYPO3Rector\CodeQuality\General\GeneralUtilityMakeInstanceToConstructorPropertyRector;
use Ssch\TYPO3Rector\CodeQuality\General\RemoveTypo3VersionChecksRector;
use Ssch\TYPO3Rector\Configuration\Typo3Option;
use Ssch\TYPO3Rector\Set\Typo3LevelSetList;
use Ssch\TYPO3Rector\Set\Typo3SetList;
use Ssch\TYPO3Rector\TYPO313\v4\MigratePluginContentElementAndPluginSubtypesRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/Build',
        __DIR__ . '/Classes',
        __DIR__ . '/Configuration',
        __DIR__ . '/Resources',
        __DIR__ . '/Tests',
        __DIR__ . '/ext_localconf.php',
    ])
    ->withSkipPath(__DIR__ . '/Tests/Acceptance/Support/_generated')
    ->withPhpSets(php82: true)
    ->withPhpVersion(PhpVersion::PHP_82)
    ->withSets([
        // Rector rules
        SetList::CODE_QUALITY,
        LevelSetList::UP_TO_PHP_82,

        Typo3SetList::CODE_QUALITY,
        Typo3SetList::GENERAL,
        Typo3LevelSetList::UP_TO_TYPO3_14,
        // To migrate to Doctrine Dbal 4, uncomment the following line
        // \Rector\Doctrine\Set\DoctrineSetList::DOCTRINE_DBAL_40,
    ])
    // To have a better analysis from PHPStan, we teach it here some more things
    ->withPHPStanConfigs([Typo3Option::PHPSTAN_FOR_RECTOR_PATH])
    ->withImportNames(false, true, false, true)
    ->withRules([
        AddVoidReturnTypeWhereNoReturnRector::class,
    ])
    ->withConfiguredRule(
        RemoveTypo3VersionChecksRector::class,
        [RemoveTypo3VersionChecksRector::TARGET_VERSION => 14]
    )
    ->withSkip([
        AddErrorCodeToExceptionRector::class,
        LogicalToBooleanRector::class,
        MigratePluginContentElementAndPluginSubtypesRector::class,
        // @see https://github.com/sabbelasichon/typo3-rector/issues/2536
        __DIR__ . '/**/Configuration/ExtensionBuilder/*',
        NameImportingPostRector::class => [
            'ClassAliasMap.php',
        ],
        ClassPropertyAssignToConstructorPromotionRector::class => [
            __DIR__ . '/Classes/Backend/EditDocumentControllerHooks.php',
        ],
        GeneralUtilityMakeInstanceToConstructorPropertyRector::class => [
            __DIR__ . '/Classes/Backend/EditDocumentControllerHooks.php',
        ],
    ]);
