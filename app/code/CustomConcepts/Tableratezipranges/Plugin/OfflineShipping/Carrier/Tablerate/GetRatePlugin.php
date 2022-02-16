<?php
// Added order and condition for the dest_zip_to filed to origin select.
namespace CustomConcepts\Tableratezipranges\Plugin\OfflineShipping\Carrier\Tablerate;

use CustomConcepts\Tableratezipranges\Helper\Config;
use Magento\Framework\DB\Select;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate\RateQuery as SubjectRateQuery;

class GetRatePlugin
{
    /**
     * @var RateRequest
     */
    private $request;

    /**
     * @var Config
     */
    private $helper;

    /**
     * @param Config $tablerateHelper
     * @param RateRequest $request
     */
    public function __construct(
        Config $tablerateHelper,
        RateRequest $request
    ) {
        $this->helper = $tablerateHelper;
        $this->request = $request;
    }

    /**
     * @param SubjectRateQuery $subject
     * @param callable $proceed
     * @param Select $select
     * @return Select
     */
    public function aroundPrepareSelect(SubjectRateQuery $subject, callable $proceed, Select $select)
    {
        if (!$this->helper->isTableRateZipRangesEnabled()) {
            return $proceed($select);
        }
        $select->where(
            'website_id = :website_id'
        )->order(
            ['dest_country_id DESC', 'dest_region_id DESC', 'dest_zip DESC', 'dest_zip_to DESC', 'condition_value DESC']
        );

        // Render destination condition
        $orWhere = '(' . implode(
            ') OR (',
            [
                    "dest_country_id = :country_id AND dest_region_id = :region_id AND dest_zip = :postcode",
                    "dest_country_id = :country_id AND dest_region_id = :region_id AND dest_zip = ''",

                    // Handle asterisk in dest_zip field
                    "dest_country_id = :country_id AND dest_region_id = :region_id AND dest_zip = '*'",
                    "dest_country_id = :country_id AND dest_region_id = 0 AND dest_zip = '*'",
                    "dest_country_id = '0' AND dest_region_id = :region_id AND dest_zip = '*'",
                    "dest_country_id = '0' AND dest_region_id = 0 AND dest_zip = '*'",
                    "dest_country_id = :country_id AND dest_region_id = 0 AND dest_zip = ''",
                    "dest_country_id = :country_id AND dest_region_id = 0 AND dest_zip = :postcode",
                    "dest_country_id = :country_id AND dest_region_id = 0 AND dest_zip = '*'",

                    // Handle the range of zipcodes
                    "dest_country_id = :country_id AND dest_region_id = :region_id AND dest_zip <= :postcode AND dest_zip_to >= :postcode",
                    "dest_country_id = :country_id AND dest_region_id = 0 AND dest_zip <= :postcode AND dest_zip_to >= :postcode",
                    "dest_country_id = '0' AND dest_region_id = :region_id AND dest_zip <= :postcode AND dest_zip_to >= :postcode",
                    "dest_country_id = '0' AND dest_region_id = 0 AND dest_zip <= :postcode AND dest_zip_to >= :postcode",
                        ]
        ) . ')';
        $select->where($orWhere);

        // Render condition by condition name
        if (is_array($this->request->getConditionName())) {
            $orWhere = [];
            foreach (range(0, count($this->request->getConditionName())) as $conditionNumber) {
                $bindNameKey = sprintf(':condition_name_%d', $conditionNumber);
                $bindValueKey = sprintf(':condition_value_%d', $conditionNumber);
                $orWhere[] = "(condition_name = {$bindNameKey} AND condition_value <= {$bindValueKey})";
            }

            if ($orWhere) {
                $select->where(implode(' OR ', $orWhere));
            }
        } else {
            $select->where('condition_name = :condition_name');
            $select->where('condition_value <= :condition_value');
        }
        return $select;
    }
}
