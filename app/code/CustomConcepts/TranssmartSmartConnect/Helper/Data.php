<?php
namespace CustomConcepts\TranssmartSmartConnect\Helper;

use Bluebirdday\TranssmartSmartConnect\Model\Booking\Profile\AllowedCountry;
use Bluebirdday\TranssmartSmartConnect\Model\Booking\ProfileRepository;
use Magento\Framework\App\Helper\Context;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var ProfileRepository
     */
    protected $profileRepository;

    /**
     * Data constructor.
     * @param Context $context
     * @param ProfileRepository $profileRepository
     */
    public function __construct(
        Context $context,
        ProfileRepository $profileRepository
    ){
        $this->profileRepository = $profileRepository;
        parent::__construct($context);
    }

    /**
     * @param string $profileCode
     * @return array
     */
    public function getDynamicConfigFields($profileCode = ''){
        $dynamicConfigFields = [];
        $path = implode('/', [AllowedCountry::CONFIG_TRANSSMART_PROFILES_SECTION_ID, $profileCode]);

        $dynamicConfigFields['title'] = [
            'id' => 'title',
            'translate' => '',
            'type' => 'text',
            'sortOrder' => '30',
            'showInDefault' => '1',
            'showInWebsite' => '1',
            'showInStore' => '1',
            'label' => __('Title'),
            '_elementType' => 'field',
            'path' => $path,
        ];

        $dynamicConfigFields['textarea'] = [
            'id' => 'textarea',
            'translate' => '',
            'type' => 'textarea',
            'sortOrder' => '40',
            'showInDefault' => '1',
            'showInWebsite' => '1',
            'showInStore' => '1',
            'label' => __('Description'),
            'comment' => __('Description for shipment to popup when user clicks or hovers over question mark.'),
            '_elementType' => 'field',
            'path' => $path,
        ];

        $dynamicConfigFields['cutoff'] = [
            'id' => 'cutoff',
            'translate' => '',
            'type' => 'text',
            'sortOrder' => '50',
            'showInDefault' => '1',
            'showInWebsite' => '1',
            'showInStore' => '1',
            'label' => __('Cutoff Time'),
            'comment' => __('For example: "14 or 16 for 12:00 or 16:00, default 16:00 Amsterdam timezone.".'),
            '_elementType' => 'field',
            'path' => $path,
        ];

        $dynamicConfigFields['tracking_method'] = [
            'id' => 'tracking_method',
            'translate' => '',
            'type' => 'select',
            'sortOrder' => '60',
            'showInDefault' => '1',
            'showInWebsite' => '1',
            'showInStore' => '1',
            'label' => __('Shipping Method'),
            'source_model' => 'CustomConcepts\\Checkout\\Model\\Config\\Source\\TrackingMethod',
            '_elementType' => 'field',
            'path' => $path,
        ];

        $dynamicConfigFields['moneyback_guarantee'] = [
            'id' => 'moneyback_guarantee',
            'translate' => '',
            'type' => 'select',
            'sortOrder' => '70',
            'showInDefault' => '1',
            'showInWebsite' => '1',
            'showInStore' => '1',
            'label' => __('Money back guarantee'),
            'source_model' => 'Magento\\Config\\Model\\Config\\Source\\Yesno',
            '_elementType' => 'field',
            'path' => $path,
        ];

        $dynamicConfigFields['home_delivery'] = [
            'id' => 'home_delivery',
            'translate' => '',
            'type' => 'select',
            'sortOrder' => '70',
            'showInDefault' => '1',
            'showInWebsite' => '1',
            'showInStore' => '1',
            'label' => __('Home Delivery'),
            'source_model' => 'Magento\\Config\\Model\\Config\\Source\\Yesno',
            'comment' => __('Set "Yes" to not require pickup location on checkout.'),
            '_elementType' => 'field',
            'path' => $path,
        ];

        return $dynamicConfigFields;
    }

    /**
     * @return array
     */
    public function getSystemXmlMapping(){
        $mappedPaths = [];

        $defaults = ['sallowspecific', 'specificcountry']; //from \Bluebirdday\TranssmartSmartConnect\Model\Config\Config\Structure\Element\SectionPlugin
        $dynamicFields = array_keys($this->getDynamicConfigFields());
        $dynamicFields = array_merge($defaults, $dynamicFields);

        $profileSectionId = AllowedCountry::CONFIG_TRANSSMART_PROFILES_SECTION_ID;


        $profiles = $this->profileRepository->getList();
        /** @var \Bluebirdday\TranssmartSmartConnect\Model\Booking\Profile $profile */
        foreach ($profiles as $index => $profile) {
            $path = $profileSectionId . '/' . $profile->getCode() . '/';

            foreach ($dynamicFields as $dynamicField){
                $mappedPaths[$path . $dynamicField] = [$path . $dynamicField];
            }
        }

        return $mappedPaths;
    }
}
