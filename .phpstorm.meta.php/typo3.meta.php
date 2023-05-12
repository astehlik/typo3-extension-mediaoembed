<?php

namespace PHPSTORM_META {

    use TYPO3\CMS\Core\Utility\GeneralUtility;
    use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;

    override(
        GeneralUtility::makeInstance(0),
        map(
            [
                '' => '@',
            ]
        )
    );

    override(
        ObjectManagerInterface::get(0),
        map(
            [
                '' => '@',
            ]
        )
    );
}
