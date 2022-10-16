<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Tests\Acceptance\Backend;

use Sto\Mediaoembed\Tests\Acceptance\Support\BackendTester;
use Sto\Mediaoembed\Tests\Acceptance\Support\Helper\ModalDialog;
use Sto\Mediaoembed\Tests\Acceptance\Support\Helper\PageTree;

class ContentCreationCest
{
    /**
     * @param BackendTester $I
     */
    public function _before(BackendTester $I)
    {
        $I->useExistingSession('admin');
    }

    public function externalMediaCanBeCreated(BackendTester $I, PageTree $pageTree, ModalDialog $modalDialog)
    {
        $I->click('Page');
        $pageTree->openPath(['root Page']);

        $I->wait(0.2);
        $I->switchToContentFrame();

        $I->click('Create new content element');

        $modalDialog->canSeeDialog();

        $I->click('External media');

        $I->switchToContentFrame();

        $headerInputSelector = 'input[data-formengine-input-name$="[header]"]';
        $I->waitForElement($headerInputSelector);
        $I->fillField($headerInputSelector, 'Testheader');

        $I->click('Create new');
    }
}
