<?php
/**
 * Copyright © 2015 CustomConcepts . All rights reserved.
 */

namespace CustomConcepts\Estimations\Helper;

use Magento\Catalog\Model\ProductRepository;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Psr\Log\LoggerInterface;

class Data extends AbstractHelper
{

    /**
     * @var Context
     */
    private $context;
    /**
     * @var DeliveryDate
     */
    protected $deliveryDateHelper;
    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;
    /**
     * @var ShippingDate
     */
    protected $shippingDateHelper;

    protected $storeManager;

    protected $productRepository;

    protected $date;

    protected $configurable;

    /**
     * Data constructor.
     * @param Context $context
     * @param DeliveryDate $deliveryDateHelper
     * @param ShippingDate $shippingDateHelper
     * @param CheckoutSession $checkoutSession
     */
    public function __construct(
        Context $context,
        \CustomConcepts\Estimations\Helper\DeliveryDate $deliveryDateHelper,
        \CustomConcepts\Estimations\Helper\ShippingDate $shippingDateHelper,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date,
        \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable $configurable
    ) {
        $this->deliveryDateHelper = $deliveryDateHelper;
        $this->shippingDateHelper = $shippingDateHelper;
        $this->checkoutSession = $checkoutSession;
        $this->storeManager = $storeManager;
        $this->productRepository = $productRepository;
        $this->date = $date;
        $this->configurable = $configurable;
        parent::__construct($context);
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * returns the EDD data of the current quote
     */
    public function getEdd($c_profile = false){
        $quote = $this->checkoutSession->getQuote();
        $max_esd = $this->shippingDateHelper->getMaxESD($quote, 'Y-m-d', $c_profile);

        return $this->deliveryDateHelper->calculateEDD($max_esd['max_esd'], $quote, $c_profile);;
    }

    /**
     * @param $profile_code
     * @return mixed
     */
    public function getShippingMethodConfig($profile_code, $store_id = null){
//        $extra_shipping_info = $this->scopeConfig->getValue("transsmart_carrier_profiles/carrierprofile_$profile_code");
        if(empty($store_id)){
            $store_id = $this->storeManager->getStore()->getId();
        }

        $extra_shipping_info = $this->scopeConfig->getValue("transsmart_profiles/$profile_code", ScopeInterface::SCOPE_STORE, $store_id);
        if($extra_shipping_info) {
            return $extra_shipping_info;
        } else {
            return [];
        }
    }

    /**
     * @param $shippingInfo
     * @param $key
     * @return mixed|null
     * used to get shipping info and catch error if its not available.
     */
    public function getShippingInfo($shippingInfo, $key){
        return isset($shippingInfo[$key]) ? $shippingInfo[$key] : null;
    }

    public function getTableRatesTitle() {
        return $this->scopeConfig->getValue('carriers/tablerate/title', ScopeInterface::SCOPE_STORE, $this->storeManager->getStore()->getId());
    }
}
