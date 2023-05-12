<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\ViewHelpers;

// phpcs:disable
if (class_exists('TYPO3Fluid\\Fluid\\Core\\ViewHelper\\AbstractViewHelper')) {
    abstract class AbstractTagBasedViewHelper extends \TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper
    {
    }
} else {
    abstract class AbstractTagBasedViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper
    {
    }
}
// phpcs:enable
