<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Tests\Acceptance\Support\Helper;

use Sto\Mediaoembed\Tests\Acceptance\Support\BackendTester;
use TYPO3\TestingFramework\Core\Acceptance\Helper\AbstractPageTree;

class PageTree extends AbstractPageTree
{
    /**
     * Inject our core AcceptanceTester actor into ModalDialog
     *
     * @param BackendTester $I
     */
    public function __construct(BackendTester $I)
    {
        $this->tester = $I;
    }
}
