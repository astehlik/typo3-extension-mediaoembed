<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Backend;

/*                                                                        *
 * This script belongs to the TYPO3 Extension "mediaoembed".              *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use Sto\Mediaoembed\Service\AspectRatioCalculator;
use Sto\Mediaoembed\Service\AspectRatioCalculatorInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final class AspectRatioEvaluation
{
    /**
     * @var AspectRatioCalculatorInterface
     */
    private $aspectRatioCalculator;

    /**
     * Server-side validation/evaluation on opening the record.
     *
     * @param array $parameters Array with key 'value' containing the field value from the database
     *
     * @return string Evaluated field value
     */
    public function deevaluateFieldValue(array $parameters): string
    {
        return (string)($parameters['value'] ?? '');
    }

    /**
     * Server-side validation/evaluation on saving the record
     * Tests if latutide is between -90 and +90, fills up with zeros to mach decimal (14,12) in database.
     *
     * @param mixed $value The field value to be evaluated
     *
     * @return string Evaluated field value
     */
    public function evaluateFieldValue($value): string
    {
        $value = trim((string)$value);

        if (!$value) {
            return '';
        }

        $aspectRatioCalculator = $this->getAspectRatioCalculator();
        return $aspectRatioCalculator->isValidAspectRatio($value) ? $value : '';
    }

    public function injectAspectRatioCalculator(AspectRatioCalculatorInterface $aspectRatioCalculator): void
    {
        $this->aspectRatioCalculator = $aspectRatioCalculator;
    }

    /**
     * JavaScript code for client side validation/evaluation.
     *
     * @return string JavaScript code for client side validation/evaluation
     */
    public function returnFieldJS(): string
    {
        return file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'AspectRatioEvaluation.js');
    }

    private function getAspectRatioCalculator(): AspectRatioCalculatorInterface
    {
        if (!$this->aspectRatioCalculator) {
            $this->aspectRatioCalculator = GeneralUtility::makeInstance(AspectRatioCalculator::class);
        }
        return $this->aspectRatioCalculator;
    }
}
