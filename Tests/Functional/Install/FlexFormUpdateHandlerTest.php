<?php

namespace Sto\Mediaoembed\Tests\Functional\Install;

use Sto\Mediaoembed\Install\FlexFormUpdateHandler;
use Sto\Mediaoembed\Install\Repository\UpdateRepositoryFactory;
use Sto\Mediaoembed\Tests\Functional\AbstractFunctionalTest;

class FlexFormUpdateHandlerTest extends AbstractFunctionalTest
{
    /**
     * @var FlexFormUpdateHandler
     */
    private $flexFormUpdateHandler;

    public function setUp()
    {
        parent::setUp();

        $updateRepository = UpdateRepositoryFactory::getUpdateRepository();
        $this->flexFormUpdateHandler = new FlexFormUpdateHandler($updateRepository);
    }

    public function testCheckForUpdateWithExistingLegacyContents()
    {
        $this->importDataSet(__DIR__ . '/../Fixtures/LegacyContentElements.xml');

        $description = '';
        $result = $this->flexFormUpdateHandler->checkForUpdate($description);

        $this->assertTrue($result);
        $this->assertContains('<strong>4</strong>', $description);
    }

    public function testCheckForUpdateWithoutLegacyContents()
    {
        $description = '';
        $result = $this->flexFormUpdateHandler->checkForUpdate($description);

        $this->assertFalse($result);
        $this->assertNotContains('<strong>', $description);
    }

    public function testPerformUpdate()
    {
        $this->importDataSet(__DIR__ . '/../Fixtures/LegacyContentElements.xml');

        $dbQueries = [];
        $customMessages = '';
        $result = $this->flexFormUpdateHandler->performUpdate($dbQueries, $customMessages);

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
