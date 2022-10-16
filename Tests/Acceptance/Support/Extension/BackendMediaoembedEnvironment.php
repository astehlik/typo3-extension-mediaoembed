<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Tests\Acceptance\Support\Extension;

use TYPO3\TestingFramework\Core\Acceptance\Extension\BackendEnvironment;

class BackendMediaoembedEnvironment extends BackendEnvironment
{
    /**
     * Load a list of core extensions and styleguide
     *
     * @var array
     */
    protected $localConfig = [
        'coreExtensionsToLoad' => [
            'core',
            'extbase',
            'fluid',
            'backend',
            'about',
            'filelist',
            'install',
            'frontend',
            'recordlist',
            'fluid_styled_content',
        ],
        'testExtensionsToLoad' => ['typo3conf/ext/mediaoembed'],
        'xmlDatabaseFixtures' => [
            'PACKAGE:typo3/testing-framework/Resources/Core/Acceptance/Fixtures/be_users.xml',
            'PACKAGE:typo3/testing-framework/Resources/Core/Acceptance/Fixtures/be_sessions.xml',
            'PACKAGE:typo3/testing-framework/Resources/Core/Acceptance/Fixtures/be_groups.xml',
            'EXT:mediaoembed/Tests/Acceptance/Fixtures/page.xml',
        ],
    ];
}
