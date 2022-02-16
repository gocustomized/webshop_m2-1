<?php

namespace CustomConcepts\Estimations\Helper;

use Magento\Catalog\Model\ProductRepository;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Psr\Log\LoggerInterface;

/**
 * Class ShippingDate
 *
 * @package CustomConcepts\Estimations\Helper
 */
class ShippingDate extends AbstractHelper
{

    /**
     * @var CheckoutSession
     */
    private $checkoutSession;
    /**
     * @var Psr\Log\LoggerInterface
     */
    private $logger;
    /**
     * @var ProductRepository
     */
    private $productRepository;
    /**
     * @var Configurable
     */
    private $catalogProductTypeConfigurable;
    /**
     * @var Context
     */
    private $context;

    protected $date;

    /**
     * @param Context $context
     * @param CheckoutSession $checkoutSession
     * @param LoggerInterface $logger
     * @param ScopeConfigInterface $scopeConfig
     * @param ProductRepository $productRepository
     * @param Configurable $catalogProductTypeConfigurable
     */
    public function __construct(
        Context $context,
        CheckoutSession $checkoutSession,
        LoggerInterface $logger,
        ScopeConfigInterface $scopeConfig,
        ProductRepository $productRepository,
        Configurable $catalogProductTypeConfigurable,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date
    ) {
        parent::__construct($context);
        $this->checkoutSession = $checkoutSession;
        $this->logger = $logger;
        $this->scopeConfig = $scopeConfig;
        $this->productRepository = $productRepository;
        $this->catalogProductTypeConfigurable = $catalogProductTypeConfigurable;
        $this->context = $context;
        $this->date = $date;
    }

    public function get($quote = null, $calculate = false, $carrier_profile_id = false, $save = true)
    {
        if ($quote == null) {
            $quote = $this->checkoutSession->getQuote();
        }
        if (!$carrier_profile_id) {
            $carrier_profile_id = $quote->getCarrierProfileId();
        }
        $producing_days = $this->getMaxProducingDays($quote);
        if ($calculate) {
            $max_esd = $this->getMaxESD($quote, 'Y-m-d', $carrier_profile_id);
            $esd = $max_esd['max_esd'];
//            $esd = $this->calculateExpectedShippingDate($producing_days, $quote, 'Y-m-d', $carrier_profile_id);
        } else {
            //@TODO Return Estimation Date from db.
            die('@TODO Return Estimation Date from db.');
        }
        return $esd;
    }

    public function getMaxProducingDays($quote)
    {
        /*
         * @TODO: Needs to be implemented correctly with magento 2 stock inventory.
         if ($oos_date = $this->getOosDate($item)) {
            return $oos_date;
        }*/

        $items = $quote->getAllItems();
        $max_producing_days = 1;
        foreach ($items as $item) {
            $item_producing_days = $this->getProducingDays($item->getProduct());
            $max_producing_days = ($max_producing_days < $item_producing_days ? $item_producing_days : $max_producing_days);
        }
        $this->logger->debug('Max producing days of quote:' . $quote->getId() . ' , days:' . $max_producing_days);
        return $max_producing_days;
    }

    public function calculateExpectedShippingDate($producing_days, $quote, $format = 'Y-m-d', $c_profile = false)
    {
        //If created_at is not available because item is still in creation and not saved yet, use now timestamp.
        if (empty($quote->getCreatedAt()) && empty($quote->getUpdatedAt())) {
            $created_at = strtotime($this->date->date()->format('Y-m-d H:i:s'));
        } elseif (!empty($quote->getUpdatedAt())) {
            $date = new \Datetime($quote->getUpdatedAt());
            $created_at = strtotime($this->date->date($date)->format('Y-m-d H:i:s'));
        } elseif (!empty($quote->getCreatedAt())) {
            $date = new \Datetime($quote->getCreatedAt());
            $created_at = strtotime($this->date->date($date)->format('Y-m-d H:i:s'));
        }
//        echo 'Created_at:'.$quote->getCreatedAt().'<br/>';
        $hour_of_day = date('G', $created_at);
//        $this->logger->debug(('created at hour:'.$hour_of_day,null,'preselected_shippingmethod.log');
//        echo 'hour_of_day:'.$hour_of_day.'<br/>';
        //If the hour of the day is after 1600 next producing day is day after else first producing day is same day.
        $carrier_profile = $c_profile ?: $quote->getShippingAddress()->getData('transsmart_carrierprofile_id');
        $cutoff = null;
        if ($carrier_profile) {
            $cutoff = $this->scopeConfig->getValue('transsmart_carrier_profiles/carrierprofile_' . $carrier_profile . '/cutoff', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $quote->getStoreId());
//            $this->logger->debug(('cutoff van carrier:'.$carrier_profile.' '.$cutoff);
        }
        if(!$cutoff) {
            $cutoff = 16;
        }

        //Get current date to calculate offset of CEST and GMT.
//        $offset = date("Z") / 3600;
//      $this->logger->debug(('offset:'.$offset,null,'preselected_shippingmethod.log');

//        $cutoff_hour = $cutoff + $offset;
        $this->logger->debug('cutoff hour:' . $cutoff . 'carrier:' . ($carrier_profile ? $carrier_profile : 'EMPTY'));
        if ($hour_of_day < $cutoff) {
            $dayx = strtotime(date('Y-m-d', $created_at) . ' -1 day');
        } else {
            $dayx = $created_at;
        }
        $this->logger->debug('Calculating shipment day based on:' . date('Y-m-d', $dayx));

        //Loop over each producing day and check if the %i date is not in the weekend otherwise skip that and go to next day.
        return $this->getNextProducingDayWithoutWeekend($dayx, $producing_days, $format);
    }

    public function getNextProducingDayWithoutWeekend($dayx, $days, $format)
    {
        $i = 1;
        while ($i <= $days) {
            $date = date('Y-m-d', $dayx);
            $dayx = strtotime($date . ' +1 day');
            $day = date('N', $dayx);

            if ($day < 6) {
                $i++;
            } else {
                // If day is in the weekend add the remaining days of the week to end at the beginning of next week.
                $daysRemaining = 8 - $day;
                $dayx = strtotime($date . ' +' . $daysRemaining . ' day');
            }
        }
//        echo sprintf('Day:%s',date('l',$dayx)) .' ';
        return date($format, $dayx);
    }

    public function getFastCarriers()
    {
        $store_config = Mage::getStoreConfig('transsmart_carrier_profiles/hide_fast_carriers/carrier_id');
        return explode(',', $store_config);
    }

    /**
     * @param $product
     * @description Method for giving the value of producing_days if it's simple and days are not set, then try to load from parent configurable.
     * If nothing is set default 1 is returned.
     * @return int
     */
    public function getProducingDays($product)
    {
        $producing_days = $product_producing_days = 1;
        $product_producing_days = $product->getProducingDays();

        $this->logger->debug('Product:' . $product->getId() . ' has ' . $product_producing_days . ' prod. days.');
        //Check if simple has specific producing day set otherwise use the one from the configurable parent product.
        $parentByChild = $this->catalogProductTypeConfigurable->getParentIdsByChild($product->getId());
        if (empty($product_producing_days) && isset($parentByChild[0])) {
            //set id as parent product id...
            $id = $parentByChild[0];
            $product_producing_days = $this->productRepository->getById($id)->getProducingDays();
            $this->logger->debug('Product prod. days empty so checking not empty parent:' . $parentByChild[0] . 'parent_producing_days.' . $product_producing_days);
        }

        //Load product because it's not there apparently after update.
        if (empty($product_producing_days)) {
            $product->load($product->getId());
            $product_producing_days = $product->getProducingDays();
            $this->logger->debug('Still empty prod days so try reloading product for prod. days:' . $product_producing_days);
        }

        //Check if the product producing days are higher then 1 otherwise show default 1 producing_day.
        if ($product_producing_days > $producing_days) {
            return $product_producing_days;
        }
        return $producing_days;
    }

    public function formatESD($esd)
    {
        return Mage::helper('core')->formatDate($esd, 'short', $showTime = false);
    }

    public function getMaxESD($quote = false, $format = 'Y-m-d', $carrier_profile_id=false){
        if(!$quote){
            $quote = $this->checkoutSession->getQuote();
        }

        $items = $quote->getAllItems();
        $oos = false;
        $dates = [];
        $max_esd = 0;

        /** @var $item \Magento\Quote\Model\Quote\Item */
        foreach ($items as $item) {
            $product = $this->productRepository->getById($item->getProduct()->getId()); //TODO: for some reason getProduct() doesnt have all attributes we need. so i need to load the product
            $producing_days = $this->getProducingDays($product);

            if($this->checkOos($product)){
                $esd = $product->getDateOnStock();
                $oos = true;
            } else {
                $esd = $this->calculateExpectedShippingDate($producing_days, $quote, 'Y-m-d', $carrier_profile_id);
            }

            $dates[] = $esd;
        }
        if ($dates) {
            $max_esd =  max(array_filter($dates));

            if($format == 'full'){
                $max_esd = $this->date->formatDate($max_esd, \IntlDateFormatter::FULL, false);
            } else {
                $max_esd = date($format, strtotime($max_esd));
            }
        }

        return [
            'max_esd' => $max_esd,
            'oos' => $oos
        ];
    }

    public function checkOos($product){
        if(!is_a($product, 'Magento\Catalog\Model\Product')){
            $product = $this->productRepository->getById($product);
        }

        $now = $this->date->date()->format('Y-m-d H:i:s');

        if($dateOnStock = $product->getDateOnStock()){
            if($dateOnStock > $now){
                return true;
            }
        } else {
            $parentIds = $this->catalogProductTypeConfigurable->getParentIdsByChild($product->getId());

            foreach ($parentIds as $parentId){
                $product = $this->productRepository->getById($parentId);

                if($dateOnStock = $product->getDateOnStock()){
                    if($dateOnStock > $now){
                        return true;
                    }
                }
            }
        }

        return false;
    }
}
