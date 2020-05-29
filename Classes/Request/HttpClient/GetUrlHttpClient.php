<?php

namespace Sto\Mediaoembed\Request\HttpClient;

use Sto\Mediaoembed\Exception\HttpClientRequestException;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class GetUrlHttpClient implements HttpClientInterface
{
    /**
     * @param string $requestUrl
     * @return string
     * @throws HttpClientRequestException
     */
    public function executeGetRequest(string $requestUrl): string
    {
        $report = [];
        $responseData = (string)GeneralUtility::getURL($requestUrl, 0, false, $report);
        if ($report['error'] === 0) {
            return $responseData;
        }

        throw new HttpClientRequestException(
            $report['message'], $this->getErrorCode($report), null,
            $report['error']
        );
    }

    /**
     * Tries to get the real error code from the $report array of
     * GeneralUtility::getURL()
     *
     * @param array $report report array of GeneralUtility::getURL()
     * @return string the error code
     * @see t3lib_div::getURL()
     */
    private function getErrorCode(array $report): string
    {
        $message = $report['message'];

        if (strstr($message, '404')) {
            return '404';
        }

        if (strstr($message, '501')) {
            return '501';
        }

        if (strstr($message, '401')) {
            return '401';
        }

        return (string)$report['error'];
    }
}
