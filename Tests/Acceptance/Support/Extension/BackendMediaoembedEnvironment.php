<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Tests\Acceptance\Support\Extension;

use Codeception\Event\SuiteEvent;
use RuntimeException;
use TYPO3\TestingFramework\Core\Acceptance\Extension\BackendEnvironment;

class BackendMediaoembedEnvironment extends BackendEnvironment
{
    /**
     * Load a list of core extensions and styleguide.
     *
     * @var array
     */
    protected $localConfig = [
        'coreExtensionsToLoad' => [
            'core',
            'extbase',
            'fluid',
            'backend',
            'filelist',
            'install',
            'frontend',
            'fluid_styled_content',
        ],
        'testExtensionsToLoad' => ['typo3conf/ext/mediaoembed'],
        'csvDatabaseFixtures' => [__DIR__ . '/../../Fixtures/BackendEnvironment.csv'],
    ];

    public function bootstrapTypo3Environment(SuiteEvent $suiteEvent): void
    {
        parent::bootstrapTypo3Environment($suiteEvent);

        $typo3RootPath = (string)getenv('TYPO3_PATH_ROOT');

        if ($typo3RootPath === '') {
            throw new RuntimeException('TYPO3_PATH_ROOT environment variable is not set');
        }

        $sysextDir = rtrim(ORIGINAL_ROOT, '/') . '/typo3/sysext';
        $rootHtaccess = $sysextDir . '/install/Resources/Private/FolderStructureTemplateFiles/root-htaccess';

        if (!file_exists($rootHtaccess)) {
            throw new RuntimeException('File not found: ' . $rootHtaccess);
        }

        copy($rootHtaccess, $typo3RootPath . '/.htaccess');

        $putenvCode = PHP_EOL
            . 'putenv(\'TYPO3_PATH_ROOT=' . $typo3RootPath . '\');' . PHP_EOL
            . 'putenv(\'TYPO3_PATH_APP=' . $typo3RootPath . '\');' . PHP_EOL
            . PHP_EOL;

        $indexFiles = ['index.php'];

        foreach ($indexFiles as $indexFile) {
            $indexPath = $typo3RootPath . '/' . $indexFile;
            $indexContexts = file_get_contents($indexPath);
            $indexContexts = str_replace('<?php', '<?php ' . $putenvCode, $indexContexts);
            file_put_contents($indexPath, $indexContexts);
        }
    }
}
