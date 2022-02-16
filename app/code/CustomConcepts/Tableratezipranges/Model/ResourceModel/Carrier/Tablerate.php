<?php

declare(strict_types=1);

namespace CustomConcepts\Tableratezipranges\Model\ResourceModel\Carrier;

use Bluebirdday\TranssmartSmartConnect\Model\ResourceModel\Carrier\Tablerate as BluebirddayTablerate;
use Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate as BaseTablerate;
use CustomConcepts\Tableratezipranges\Helper\Config;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\OfflineShipping\Model\Carrier\Tablerate as CoreTablerate;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Filesystem;
use Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate\Import;
use Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate\RateQuery;
use Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate\RateQueryFactory;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class Tablerate extends BluebirddayTablerate
{
    /**
     * @var BaseTablerate\RateQueryFactory
     */
    private $rateQueryFactory;

    /**
     * @var Config
     */
    private $helper;

    /**
     * @param Config $tableRateZipRangesHelper
     * @param Context $context
     * @param LoggerInterface $logger
     * @param ScopeConfigInterface $coreConfig
     * @param StoreManagerInterface $storeManager
     * @param CoreTablerate $carrierTablerate
     * @param Filesystem $filesystem
     * @param Import $import
     * @param RateQueryFactory $rateQueryFactory
     * @param null $connectionName
     */
    public function __construct(
        Config $tableRateZipRangesHelper,
        Context $context,
        LoggerInterface $logger,
        ScopeConfigInterface $coreConfig,
        StoreManagerInterface $storeManager,
        CoreTablerate $carrierTablerate,
        Filesystem $filesystem,
        Import $import,
        RateQueryFactory $rateQueryFactory,
        $connectionName = null
    ) {
        $this->helper = $tableRateZipRangesHelper;
        $this->rateQueryFactory = $rateQueryFactory;
        parent::__construct(
            $context,
            $logger,
            $coreConfig,
            $storeManager,
            $carrierTablerate,
            $filesystem,
            $import,
            $rateQueryFactory,
            $connectionName
        );
    }

    /**
     * Return table rate array or false by rate request
     *
     * @param \Magento\Quote\Model\Quote\Address\RateRequest $request
     * @return array|bool
     */
    public function getRates(\Magento\Quote\Model\Quote\Address\RateRequest $request)
    {
        $result = array();
        $connection = $this->getConnection();

        $rate = parent::getRate($request);
        $select = $connection->select()->from($this->getMainTable());
//        /** @var RateQuery $rateQuery */
//        $rateQuery = $this->rateQueryFactory->create(['request' => $request]);

//        $rateQuery->prepareSelect($select);
//        $bindings = $rateQuery->getBindings();
        $uniqueFields = array(
            'website_id',
            'dest_country_id',
            'dest_region_id',
            'dest_zip',
            'condition_name',
            'condition_value'
        );
        // add query conditions
        foreach ($rate as $_field => $_value) {
            if (in_array($_field, $uniqueFields)) {
                if ($_field == 'dest_zip' && ($_value === '' || $_value == '*')) {
                    $select->where("`dest_zip` IN ('', '*')");
                } /* Added logic to also support tablerate ranges.*/
                elseif ($_field == 'dest_zip') {
                    $select->where("(`dest_zip` <= ? AND `dest_zip_to` >= ?) OR `dest_zip` = '*' ", $_value, $_value);
                } else {
                    $select->where($connection->quoteIdentifier($_field) . ' = ?', $_value);
                }
            }
        }
        $carrier_profile_rates = array();
        // run the query and process results
        if (($result = $connection->fetchAll($select))) {
            foreach ($result as $_rate) {
                // normalize destination zip code
                if ($_rate['dest_zip'] == '*') {
                    $_rate['dest_zip'] = '';
                }
                // Check if transsmart carrier profile rate not already given otherwise pick the one with selected zipcode range.
                if (!array_key_exists($_rate['transsmart_bookingprofile_id'], $carrier_profile_rates)) {
                    $carrier_profile_rates[$_rate['transsmart_bookingprofile_id']] = $_rate;
                } elseif ($_rate['dest_zip'] != '' && $_rate['dest_zip_to'] != '') {
                    $carrier_profile_rates[$_rate['transsmart_bookingprofile_id']] = $_rate;
                }
            }

            $result = $carrier_profile_rates;
        } else {
            // something went wrong, just return the single found rate
            $result = array($rate);
        }
        return $result;
    }

    /**
     * Save import data batch
     * add dest_zip_to column
     * @inheritDoc
     */
    protected function _saveImportData(array $data)
    {
        if (!empty($data)) {
            $columns = [
                'website_id',
                'dest_country_id',
                'dest_region_id',
                'dest_zip',
                'condition_name',
                'condition_value',
                'price',
                'transsmart_carrierprofile_id',
                'dest_zip_to'
            ];
            $this->getConnection()->insertArray($this->getMainTable(), $columns, $data);
            $this->_importedRows += count($data);
        }

        return $this;
    }
}
