<?php

namespace CustomConcepts\Tableratezipranges\Plugin\Transsmart\Carrier\Tablerate\CSV;

use Bluebirdday\TranssmartSmartConnect\Model\ResourceModel\Carrier\Tablerate\CSV\RowParser;
use CustomConcepts\Tableratezipranges\Helper\Config;
use Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate\CSV\ColumnNotFoundException;
use Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate\CSV\ColumnResolver;

class RowParserPlugin
{
    /**
     * @var Config
     */
    private $helper;

    /**
     * @param Config $helper
     */
    public function __construct(Config $helper)
    {
        $this->helper = $helper;
    }

    /**
     * @param RowParser $subject
     * @param $result
     * @return mixed
     */
    public function afterGetColumns(RowParser $subject, $result)
    {
        if ($this->helper->isTableRateZipRangesEnabled()) {
            $result[] = 'dest_zip_to';
        }

        return $result;
    }

    /**
     * @param RowParser $subject
     * @param array $result
     * @param array $rowData
     * @param $rowNumber
     * @param $websiteId
     * @param $conditionShortName
     * @param $conditionFullName
     * @param ColumnResolver $columnResolver
     * @return array
     * @throws ColumnNotFoundException
     */
    public function afterParse(
        RowParser $subject,
        array $result,
        array $rowData,
        $rowNumber,
        $websiteId,
        $conditionShortName,
        $conditionFullName,
        ColumnResolver $columnResolver
    ) {
        if ($this->helper->isTableRateZipRangesEnabled()) {
            $result[0]['dest_zip_to'] = $columnResolver->getColumnValue('dest_zip_to', $rowData);
        }

        return $result;
    }
}
