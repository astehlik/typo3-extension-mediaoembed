<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Tests\Acceptance\Application;

use Sto\Mediaoembed\Tests\Acceptance\Support\BackendTester;
use Sto\Mediaoembed\Tests\Acceptance\Support\Helper\ModalDialog;
use Sto\Mediaoembed\Tests\Acceptance\Support\Helper\PageTree;

class ContentCreationCest
{
    public function _before(BackendTester $I): void
    {
        $I->useExistingSession('admin');
    }

    public function html5MediaCanBeCreated(BackendTester $I, PageTree $pageTree, ModalDialog $modalDialog): void
    {
        $I->click('Page');
        $pageTree->openPath(['root Page']);

        $I->wait(0.2);
        $I->switchToContentFrame();

        $I->click('typo3-backend-new-content-element-wizard-button');

        $modalDialog->canSeeDialog();
        $I->executeJS(
            'document.querySelector(\'typo3-backend-new-record-wizard\').shadowRoot'
            . '.querySelector(\'button[data-identifier="special"]\').click()',
        );
        $I->executeJS(
            'document.querySelector(\'typo3-backend-new-record-wizard\').shadowRoot'
            . '.querySelector(\'button[data-identifier="special_mediaoembed_oembedmediarenderer"]\').click()',
        );

        $I->switchToContentFrame();

        $headerInputSelector = 'input[data-formengine-input-name$="[header]"]';
        $urlInputSelector = 'input[data-formengine-input-name$="[tx_mediaoembed_url]"]';

        $I->waitForElement($headerInputSelector);

        $I->fillField($headerInputSelector, 'Testheader');
        $I->fillField($urlInputSelector, 'https://www.youtube.com/watch?v=83lw-UQFQBo');

        $I->click('Save');

        $I->waitForElement($headerInputSelector);

        $I->dontSeeInSource('alert-danger');

        $I->seeInField($headerInputSelector, 'Testheader');
        $I->seeInField($urlInputSelector, 'https://www.youtube.com/watch?v=83lw-UQFQBo');
    }
}
