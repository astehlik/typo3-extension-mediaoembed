<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Tests\Functional\Install;

use Sto\Mediaoembed\Install\FlexFormUpdateHandler;
use Sto\Mediaoembed\Install\Repository\UpdateRepository;
use Sto\Mediaoembed\Install\Repository\UpdateRepositoryFactory;
use Sto\Mediaoembed\Tests\Functional\AbstractFunctionalTest;

class FlexFormUpdateHandlerTest extends AbstractFunctionalTest
{
    /**
     * @var UpdateRepository
     */
    private $updateRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->updateRepository = UpdateRepositoryFactory::getUpdateRepository();
    }

    public function testCheckForUpdateWithExistingLegacyContents(): void
    {
        $this->importDataSet(__DIR__ . '/../Fixtures/LegacyContentElements.xml');

        $flexFormUpdateHandler = new FlexFormUpdateHandler($this->updateRepository);
        $result = $flexFormUpdateHandler->checkForUpdate();
        $description = $flexFormUpdateHandler->getDescription();

        self::assertTrue($result);
        self::assertContains('There are currently 4', $description);
    }

    public function testCheckForUpdateWithoutLegacyContents(): void
    {
        $flexFormUpdateHandler = new FlexFormUpdateHandler($this->updateRepository);
        $result = $flexFormUpdateHandler->checkForUpdate();
        $description = $flexFormUpdateHandler->getDescription();

        self::assertFalse($result);
        self::assertNotContains('There are currently', $description);
    }

    public function testPerformUpdate(): void
    {
        $this->importDataSet(__DIR__ . '/../Fixtures/LegacyContentElements.xml');

        $dbQueries = [];
        $customMessages = '';
        $flexFormUpdateHandler = new FlexFormUpdateHandler($this->updateRepository);
        $result = $flexFormUpdateHandler->performUpdate($dbQueries, $customMessages);

        self::assertContains('Skipping content element with uid 4 because mmFile is not set', $customMessages);
        self::assertContains('Tried to update 3 records.', $customMessages);
        self::assertContains('Updated 3 records successfully.', $customMessages);
        self::assertNotContains('SQL-Error', $customMessages);

        self::assertFalse($result);

        self::assertCount(3, $dbQueries);

        $db = $this->getDatabaseConnection();

        self::assertSame(
            1,
            $db->selectCount(
                'uid',
                'tt_content',
                'CType=' . $this->dbQuoteString('mediaoembed_oembedmediarenderer')
                . ' AND uid=' . 1
                . ' AND pi_flexform=' . $this->dbQuoteString('')
                . ' AND tx_mediaoembed_maxwidth=200'
                . ' AND tx_mediaoembed_maxheight=300'
            )
        );
    }

    private function dbQuoteString($string)
    {
        return "'" . $string . "'";
    }
}
