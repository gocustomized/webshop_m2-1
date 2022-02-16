<?php
namespace CustomConcepts\Checkout\Plugin\Block;

use Magento\Framework\App\Config;
use Magento\Store\Model\ScopeInterface;

class CheckoutLayoutProcessor
{
    /**
     * Address fields sort order for hm_de, de_de, da_dk, at_at
     * */
    const ADDRESS_SORT_DE_DK_AT = ['firstname', 'lastname', 'company', 'address_autofill_nl', 'address_autofill_intl', 'street', 'postcode', 'city', 'country_id', 'region', 'region_id', 'telephone'];

    /**
     * Address fields sort order for hm_nl, nl_nl, be_fr, be_nl, gsmhm_be
     * */
    const ADDRESS_SORT_NL_BE = ['firstname', 'lastname', 'company', 'country_id', 'address_autofill_nl', 'address_autofill_intl', 'street', 'postcode', 'city', 'region', 'region_id', 'telephone'];

    /**
     * Address fields sort order for us_en, hm_us, uk_en, hm_uk, hm_es, es_es, us_es, fr_fr, hm_fr, se_se, it_it
     * @var array
     */
    const ADDRESS_SORT_US_UK_ES_FR_IT_SE = ['firstname', 'lastname', 'company', 'street', 'postcode', 'city', 'region', 'region_id', 'country_id', 'telephone'];

    /**
     * Address line sort (street -  house number - additional)
     * @var array
     */
    const DEFAULT_ADDRESS_LINE_SORT = [10,20,30];

    /**
     * Address line sort house no first (house number - street - additional)
     * @var array
     */
    const HOUSE_NO_FIRST_ADDRESS_LINE_SORT = [20,10,30];

    /**
     * Street line require street name only
     * @var array
     */
    const REQUIRE_STREET_ONLY = ['0'];
    const HAS_REQUIRE_STREET_ONLY = [];

    /**
     * Street line require street name and house number
     * @var array
     */
    const REQUIRE_STREET_AND_HOUSENO = ['0','1'];
    const HAS_REQUIRE_STREET_AND_HOUSENO = ['hm_nl','hm_de','gsmhm_be','hm_fr','hm_us','hm_uk','at_at','de_de','be_nl','nl_nl','be_fr','fr_fr','da_dk','es_es','se_se','us_es','uk_en','us_en','hm_es','it_it'];

    /**
     * Street line require street name, house number and additional
     * @var array
     */
    const REQUIRE_ALL = ['0','1','2'];
    const HAS_REQUIRE_ALL = [];

    /**
     * Region/region_id required
     * @var array
     */
    const HAS_REQUIRED_REGION_FIELD = [];
    const HAS_REQUIRED_REGION_ID_FIELD = ['es_es','it_it','us_es','us_en','hm_es','hm_us'];

    const POSTCODE_API_ENABLE_CONFIG = 'postcodenl_api/general/enabled';

    protected $storeManager;

    protected $store;

    /**
     * @var Config
     */
    private $config;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        Config $config
    ){
        $this->storeManager = $storeManager;
        $this->config = $config;
    }

    public function afterProcess(
        \Magento\Checkout\Block\Checkout\LayoutProcessor $subject,
        $jsLayout
    ) {
        $shippingAddressFieldSet = isset($jsLayout['components']['checkout']['children']['steps']
        ['children']['shipping-step']['children']['shippingAddress']
        ['children']['shipping-address-fieldset']['children']) ? $jsLayout['components']['checkout']['children']['steps']
        ['children']['shipping-step']['children']['shippingAddress']
        ['children']['shipping-address-fieldset']['children'] : [];

        $billingAddressFieldSet = isset($jsLayout['components']['checkout']['children']['steps']
        ['children']['billing-step']['children']['payment']['children']['afterMethods']['children']
        ['billing-address-form']['children']['form-fields']['children']) ? $jsLayout['components']['checkout']['children']['steps']
        ['children']['billing-step']['children']['payment']['children']['afterMethods']['children']
        ['billing-address-form']['children']['form-fields']['children'] : [];

        $hasBillingAddressFieldSet = 0;
        $this->store = $this->storeManager->getStore();

        if($billingAddressFieldSet) {
            $hasBillingAddressFieldSet = 1;
            $billingAddressFieldSet['street']['children'][0]['label'] = __('Street Name');
            $billingAddressFieldSet['street']['children'][0]['validation']['max_text_length'] = 64;
            $billingAddressFieldSet['street']['children'][1]['label'] = __('House Number');
            $billingAddressFieldSet['street']['children'][1]['validation']['max_text_length'] = 4;
            $billingAddressFieldSet['street']['children'][2]['label'] = __('Additional Address');
            $billingAddressFieldSet['street']['children'][2]['validation']['max_text_length'] = 64;
        }

        if($shippingAddressFieldSet){
            /** config for all stores */
            $shippingAddressFieldSet['street']['children'][0]['label'] = __('Street Name');
            $shippingAddressFieldSet['street']['children'][0]['validation']['max_text_length'] = 64;
            $shippingAddressFieldSet['street']['children'][1]['label'] = __('House Number');
            $shippingAddressFieldSet['street']['children'][1]['validation']['max_text_length'] = 4;
            $shippingAddressFieldSet['street']['children'][2]['label'] = __('Additional Address');
            $shippingAddressFieldSet['street']['children'][2]['validation']['max_text_length'] = 64;

            switch ($this->store->getCode()){
                case 'hm_nl':
                case 'nl_nl':
                case 'be_fr':
                case 'be_nl':
                case 'gsmhm_be':
                    $this->configureSort($shippingAddressFieldSet, self::ADDRESS_SORT_NL_BE, self::DEFAULT_ADDRESS_LINE_SORT);
                    if($billingAddressFieldSet){
                        $this->configureSort($billingAddressFieldSet, self::ADDRESS_SORT_NL_BE, self::DEFAULT_ADDRESS_LINE_SORT);
                    }
                    break;
                case 'hm_de':
                case 'de_de':
                case 'da_dk':
                case 'at_at':
                    $this->configureSort($shippingAddressFieldSet, self::ADDRESS_SORT_DE_DK_AT, self::DEFAULT_ADDRESS_LINE_SORT);
                    if($billingAddressFieldSet){
                        $this->configureSort($billingAddressFieldSet, self::ADDRESS_SORT_DE_DK_AT, self::DEFAULT_ADDRESS_LINE_SORT);
                    }
                    break;
                case 'hm_es':
                case 'es_es':
                case 'se_se':
                    case 'it_it':
                    $this->configureSort($shippingAddressFieldSet, self::ADDRESS_SORT_US_UK_ES_FR_IT_SE, self::DEFAULT_ADDRESS_LINE_SORT);
                    if($billingAddressFieldSet){
                        $this->configureSort($billingAddressFieldSet, self::ADDRESS_SORT_US_UK_ES_FR_IT_SE, self::DEFAULT_ADDRESS_LINE_SORT);
                    }
                    break;
                case 'us_en':
                case 'hm_us':
                case 'uk_en':
                case 'hm_uk':
                case 'us_es':
                case 'fr_fr':
                case 'hm_fr':
                    $this->configureSort($shippingAddressFieldSet, self::ADDRESS_SORT_US_UK_ES_FR_IT_SE, self::HOUSE_NO_FIRST_ADDRESS_LINE_SORT);
                    if($billingAddressFieldSet){
                        $this->configureSort($billingAddressFieldSet, self::ADDRESS_SORT_US_UK_ES_FR_IT_SE, self::HOUSE_NO_FIRST_ADDRESS_LINE_SORT);
                    }
                    break;
            }

            $this->setRequiredStreetAddressLines($shippingAddressFieldSet, $this->store->getCode());
            $this->setRequiredRegionField($shippingAddressFieldSet, $this->store->getCode());


            $jsLayout['components']['checkout']['children']['steps']
            ['children']['shipping-step']['children']['shippingAddress']
            ['children']['shipping-address-fieldset']['children'] = $shippingAddressFieldSet;
        }

        if($hasBillingAddressFieldSet) {
            $this->setRequiredStreetAddressLines($billingAddressFieldSet, $this->store->getCode());
            $this->setRequiredRegionField($billingAddressFieldSet, $this->store->getCode());

            $this->configurePostcodeNlApiOnBilling($billingAddressFieldSet, $shippingAddressFieldSet);

            $jsLayout['components']['checkout']['children']['steps']
            ['children']['billing-step']['children']['payment']['children']['afterMethods']['children']
            ['billing-address-form']['children']['form-fields']['children'] = $billingAddressFieldSet;
        }

        return $jsLayout;
    }

    /**
     * Sort order of fields
     * @param $addressFieldSet array|[shiipping|billing fieldsset]
     * @param @sortOrder array
     * @return array
     */
    private function sortFields(&$addressFieldSet, $sortOrder){
        $index = 1;
        foreach ($sortOrder as $order){
            if(isset($addressFieldSet[$order])){
                $addressFieldSet[$order]['sortOrder'] = $index;
                $index += 1;
            }
        }
    }

    /**
     * Sort order of street address fields
     * @param $addressFieldSet array|[shiipping|billing fieldsset]
     * @param @sortOrder array
     * @return array
     */
    private function sortStreetFields(&$addressFieldSet, $sortOrder){
        foreach ($addressFieldSet as $key => $value){
            if(isset($addressFieldSet[$key])){
                $addressFieldSet[$key]['sortOrder'] = $sortOrder[$key];
            }
        }
    }

    /**
     * Sets what street address fields to be set as required entry
     * @param $addressFieldSet array|[shiipping|billing fieldsset]
     * @param @storeCode string
     * @return array
     */
    private function setRequiredStreetAddressLines(&$addressFieldSet, $storeCode){
        $streetlines = [];

        if (in_array($storeCode, self::HAS_REQUIRE_STREET_ONLY)) {
            $streetlines = self::REQUIRE_STREET_ONLY;
        } elseif (in_array($storeCode, self::HAS_REQUIRE_STREET_AND_HOUSENO)) {
            $streetlines = self::REQUIRE_STREET_AND_HOUSENO;
        } elseif (in_array($storeCode, self::HAS_REQUIRE_ALL)) {
            $streetlines = self::REQUIRE_ALL;
        }
        foreach ($addressFieldSet['street']['children'] as $key => $value){
            if(in_array($key, $streetlines)){
                $addressFieldSet['street']['children'][$key]['validation']['required-entry'] = true;
            }
        }
    }

    /**
     * Sets region/region_id field as not required entry
     * @param $addressFieldSet array|[shiipping|billing fieldsset]
     * @param @storeCode string
     * @return array
     */
    private function setRequiredRegionField(&$addressFieldSet, $storeCode){
        unset($addressFieldSet['region']['validation']['required-entry']);
        unset($addressFieldSet['region_id']['validation']['required-entry']);

        if (in_array($storeCode, self::HAS_REQUIRED_REGION_FIELD)) {
            $addressFieldSet['region']['validation']['required-entry'] = true;
        } elseif (in_array($storeCode, self::HAS_REQUIRED_REGION_ID_FIELD)) {
            $addressFieldSet['region_id']['validation']['required-entry'] = true;
        }
    }

    /**
     * Enable postcodenl api in billing address
     * @param $billingFields array|[billing fieldsset]
     * @param $shippingFields array|[shiipping fieldsset]
     * @param @storeCode string
     * @return array
     */
    private function configurePostcodeNlApiOnBilling(&$billingFields, $shippingFields){
        $storeId = $this->store->getStoreId();
        $path = self::POSTCODE_API_ENABLE_CONFIG;
        if ($this->config->getValue($path, ScopeInterface::SCOPE_STORE, $storeId)) {
            $billingFields = array_merge($billingFields, array_intersect_key($shippingFields, ['address_autofill_nl' => 1, 'address_autofill_intl' => 1, 'address_autofill_formatted_output' => 1]));
        }
    }

    /**
     * Configure address fieldset fields
     * @param $addressFieldSet array|[shiipping|billing fieldsset]
     * @param @addressFieldsSort array
     * @param @addressLineSort array
     * @return array
     */
    private function configureSort(&$addressFieldSet, $addressFieldsSort, $addressLineSort){
        $this->sortFields($addressFieldSet, $addressFieldsSort);
        $this->sortStreetFields($addressFieldSet['street']['children'], $addressLineSort);
    }
}
