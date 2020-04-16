<?php

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

    public function setUp()
    {
        parent::setUp();

        $this->updateRepository = UpdateRepositoryFactory::getUpdateRepository();
    }

    public function testCheckForUpdateWithExistingLegacyContents()
    {
        $this->importDataSet(__DIR__ . '/../Fixtures/LegacyContentElements.xml');

        $flexFormUpdateHandler = new FlexFormUpdateHandler($this->updateRepository);
        $result = $flexFormUpdateHandler->checkForUpdate();
        $description = $flexFormUpdateHandler->getDescription();

        $this->assertTrue($result);
        $this->assertContains('There are currently 4', $description);
    }

    public function testCheckForUpdateWithoutLegacyContents()
    {
        $flexFormUpdateHandler = new FlexFormUpdateHandler($this->updateRepository);
        $result = $flexFormUpdateHandler->checkForUpdate();
        $description = $flexFormUpdateHandler->getDescription();

        $this->assertFalse($result);
        $this->assertNotContains('There are currently', $description);
    }

    public function testPerformUpdate()
    {
        $this->importDataSet(__DIR__ . '/../Fixtures/LegacyContentElements.xml');

        $dbQueries = [];
        $customMessages = '';
        $flexFormUpdateHandler = new FlexFormUpdateHandler($this->updateRepository);
        $result = $flexFormUpdateHandler->performUpdate($dbQueries, $customMessages);

        $this->assertContains('Skipping content element with uid 4 because mmFile is not set', $customMessages);
        $this->assertContains('Tried to update 3 records.', $customMessages);
        $this->assertContains('Updated 3 records successfully.', $customMessages);
        $this->assertNotContains('SQL-Error', $customMessages);

        $this->assertFalse($result);

        $this->assertCount(3, $dbQueries);

        $db = $this->getDatabaseConnection();

        $this->assertEquals(
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
