<?php
namespace CustomConcepts\TranssmartSmartConnect\Model\Config\Config\Structure\Element;

use Bluebirdday\TranssmartSmartConnect\Model\Booking\Profile\AllowedCountry;
use Magento\Config\Model\Config\Structure\Element\Section as OriginalSection;

/** this class extends the dynamic fields from \Bluebirdday\TranssmartSmartConnect\Model\Config\Config\Structure\Element\SectionPlugin */
class SectionPlugin
{
    /**
     * @var \CustomConcepts\TranssmartSmartConnect\Helper\Data
     */
    protected $tscHelper;

    /**
     * SectionPlugin constructor.
     * @param \CustomConcepts\TranssmartSmartConnect\Helper\Data $tscHelper
     */
    public function __construct(
        \CustomConcepts\TranssmartSmartConnect\Helper\Data $tscHelper
    ){
        $this->tscHelper = $tscHelper;
    }

    /**
     * @param OriginalSection $subject
     * @param array $data
     * @param $scope
     * @return array
     */
    public function beforeSetData(OriginalSection $subject, array $data, $scope) {
        if($data['id'] == AllowedCountry::CONFIG_TRANSSMART_PROFILES_SECTION_ID) {
            if (array_key_exists('children', $data)) {
                foreach ($data['children'] as $key => &$value){
                    $value['showInWebsite'] = '1';
                    $value['showInStore'] = '1';
                    $value['children'] += $this->tscHelper->getDynamicConfigFields($key);
                }
            }
        }

        return [$data, $scope];
    }
}
