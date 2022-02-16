<?php
namespace CustomConcepts\Estimations\Model;

use Magento\Checkout\Model\ConfigProviderInterface;

class EstimationConfigProvider implements ConfigProviderInterface
{
    /**
     * @var \CustomConcepts\Estimations\Helper\Data
     */
    protected $estimationsHelper;

    protected $shippingDateHelper;

    protected $date;

    /**
     * EstimationConfigProvider constructor.
     * @param \CustomConcepts\Estimations\Helper\Data $estimationsHelper
     */
    public function __construct(
        \CustomConcepts\Estimations\Helper\Data $estimationsHelper,
        \CustomConcepts\Estimations\Helper\ShippingDate $shippingDateHelper,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date
    ){
        $this->estimationsHelper = $estimationsHelper;
        $this->shippingDateHelper = $shippingDateHelper;
        $this->date = $date;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getConfig() {
        $config = [];
        $edd = $this->estimationsHelper->getEdd();
        $config['min'] = $edd->getData('min_delivery_date') ? $this->date->formatDate($edd->getData('min_delivery_date'), \IntlDateFormatter::FULL, false) : null;
        $config['max'] = $edd->getData('max_delivery_date') ? $this->date->formatDate($edd->getData('max_delivery_date'), \IntlDateFormatter::FULL, false) : null;
        $config['shippingDate'] = $edd->getData('shipping_date') ? $this->date->formatDate($edd->getData('shipping_date'), \IntlDateFormatter::FULL, false) : null;

        $maxEsd = $this->shippingDateHelper->getMaxESD(false, 'full');
        $config['max_esd'] = $maxEsd['max_esd'];
        $config['oos'] = $maxEsd['oos'];

        $config['table_rates_title'] = $this->estimationsHelper->getTableRatesTitle();

        return $config;
    }
}
