<?php

namespace CustomConcepts\Estimations\Observer\Sales;

use CustomConcepts\Estimations\Helper\Estimation;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class QuoteAddItem
 *
 * @package CustomConcepts\Estimations\Observer\Sales
 */
class AddEstimationToOrderEmail implements ObserverInterface
{

    /**
     * @var Estimation
     */
    private $estimation;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param \CustomConcepts\Estimations\Helper\Estimation $estimation
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Estimation $estimation,
        ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date
        )
    {
        $this->estimation = $estimation;
        $this->scopeConfig = $scopeConfig;
        $this->date = $date;
    }

    /**
     * Execute observer
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(
        Observer $observer
    ) {
        $transportObj = $observer->getTransport();
        $order = $transportObj->getOrder();
        $storeId = $order->getStoreId();
        $estimation = $this->estimation->getLatestCalculated($order->getId());
        //Get locale from ordered store. And set formatting date to this.
        $locale = $this->scopeConfig->getValue('general/locale/code', ScopeInterface::SCOPE_STORE, $storeId);
        if ($estimation) {
            $transportObj['estimation'] = $estimation; //cannot access array on email template
            $transportObj['ESD'] = $this->formatDate($estimation['shipping_date'], $locale);
            $transportObj['EDD_MIN'] = $this->formatDate($estimation['min_delivery_date'], $locale);
            $transportObj['EDD_MAX'] = $this->formatDate($estimation['max_delivery_date'], $locale);
            if ($transportObj['EDD_MIN'] == $transportObj['EDD_MAX']) {
                unset($transportObj['EDD_MAX']); //unset EDD_MAX so on the template we do not display date range for delivery date
            }
        } else {
            $transportObj['estimation'] = null;
        }
    }

    /**
     * @param $date date string
     * @param null $locale local value
     * @return false|string
     * @throws \Exception
     */
    protected function formatDate($date, $locale = null){
        $date = new \DateTime($date);
        $formatter = new \IntlDateFormatter($locale, \IntlDateFormatter::SHORT, \IntlDateFormatter::SHORT);
        $formatter->setPattern('dd-MM-Y');

        return $formatter->format($date);
    }
}
