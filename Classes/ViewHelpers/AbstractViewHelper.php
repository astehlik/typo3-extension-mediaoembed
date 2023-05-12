<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\ViewHelpers;

// phpcs:disable
if (class_exists('TYPO3Fluid\\Fluid\\Core\\ViewHelper\\AbstractViewHelper')) {
    abstract class AbstractViewHelper extends \TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper
    {
    }
} else {
    abstract class AbstractViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
    {
    }
}
// phpcs:enable
