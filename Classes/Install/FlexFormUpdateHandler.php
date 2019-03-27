<?php
declare(strict_types=1);

namespace Sto\Mediaoembed\Install;

use Sto\Mediaoembed\Install\Repository\UpdateRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class FlexFormUpdateHandler
{
    /**
     * @var int
     */
    private $oldRecordCount;

    /**
     * @var UpdateRepository
     */
    private $updateRepository;

    public function __construct(UpdateRepository $updateRepository)
    {
        $this->oldRecordCount = $updateRepository->countOldRecords();

        $this->updateRepository = $updateRepository;
    }

    /**
     * Checks whether updates are required.
     *
     * @param string &$description : The description for the update
     * @return boolean Whether an update is required (TRUE) or not (FALSE)
     */
    public function checkForUpdate(&$description)
    {
        $description = $this->getDescription();

        if ($this->oldRecordCount > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function getDescription(): string
    {
        $description = 'All media content elements that use oEmbed as their render type will be migrated'
            . ' to mediaoembed content elements to be compatible with the current version.';

        $description .= ' There are currently ' . $this->oldRecordCount . ' records to update.';

        return $description;
    }

    /**
     * Performs the accordant updates.
     *
     * @param array &$dbQueries : queries done in this update
     * @param mixed &$customMessages : custom messages
     * @return boolean Whether everything went smoothly or not
     */
    public function performUpdate(array &$dbQueries, &$customMessages): bool
    {
        $result = $this->updateRepository->findAllRecordsThatNeedUpgrading();

        $updateCounter = 0;
        $updateCounterSuccess = 0;

        $hasError = false;

        while ($row = $this->updateRepository->fetchResultRow($result)) {
            $flexFormData = GeneralUtility::xml2array($row['pi_flexform']);

            if (!is_array($flexFormData)) {
                $customMessages .= sprintf(
                    'Skipping content element with uid %d because of XML parsing error: %s' . "\n",
                    $row['uid'],
                    $flexFormData
                );
                $hasError = true;
                continue;
            }

            if (!isset($flexFormData['data']['sVideo']['lDEF']['mmFile']['vDEF'])
                || empty($flexFormData['data']['sVideo']['lDEF']['mmFile']['vDEF'])
            ) {
                $customMessages .= sprintf(
                    'Skipping content element with uid %d because mmFile is not set' . "\n",
                    $row['uid']
                );
                $hasError = true;
                continue;
            }

            $flexFormGeneralData = $flexFormData['data']['sGeneral']['lDEF'];
            $mediaUrl = $flexFormData['data']['sVideo']['lDEF']['mmFile']['vDEF'];

            if ($flexFormGeneralData['mmRenderType']['vDEF'] !== UpdateRepository::RENDER_TYPE) {
                continue;
            }

            $updateData = [
                'CType' => 'mediaoembed_oembedmediarenderer',
                'pi_flexform' => '',
                'tx_mediaoembed_maxwidth' => $this->getIntValueFromFlexForm($flexFormGeneralData, 'mmWidth'),
                'tx_mediaoembed_maxheight' => $this->getIntValueFromFlexForm($flexFormGeneralData, 'mmHeight'),
                'tx_mediaoembed_url' => $mediaUrl,
            ];

            $sqlError = $this->updateRepository->executeUpdateQuery((int)$row['uid'], $updateData, $dbQueries);
            $updateCounter++;

            if ($sqlError !== '') {
                $hasError = true;
                $customMessages .= 'SQL-Error: ' . $sqlError . PHP_EOL;
                continue;
            }

            $updateCounterSuccess++;
        }

        $customMessages .= 'Tried to update ' . $updateCounter . ' records.' . PHP_EOL;
        $customMessages .= 'Updated ' . $updateCounterSuccess . ' records successfully.' . PHP_EOL;

        return !$hasError;
    }

    protected function getIntValueFromFlexForm(array $flexFormData, string $field): int
    {
        if (!isset($flexFormData[$field]['vDEF'])) {
            return 0;
        }

        return (int)$flexFormData[$field]['vDEF'];
    }
}
