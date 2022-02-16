<?php

namespace CustomConcepts\Estimations\Helper;

use CustomConcepts\Estimations\Model\DeliveryRateFactory;
use CustomConcepts\Estimations\Model\EstimationDatesFactory;
use CustomConcepts\Estimations\Model\EstimationDatesRepository;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Psr\Log\LoggerInterface;

/**
 * Class DeliveryDate
 *
 * @package CustomConcepts\Estimations\Helper
 */
class DeliveryDate extends AbstractHelper
{
    /**
     * @var ShippingDate
     */
    private $shippingDateH;
    /**
     * @var psrLogInterface
     */
    private $logger;
    /**
     * @var CustomConcepts\Estimations\Model\DeliveryRateFactory
     */
    private $deliveryRateFactory;
    /**
     * @var CustomConcepts\Estimations\Model\EstimationDatesFactory
     */
    private $estimationDatesFactory;
    /**
     * @var EstimationDatesRepository
     */
    private $estimationDatesRepository;

    /**
     * @param Context $context
     * @param ShippingDate $shippingDateH
     * @param psrLogInterface $logger
     * @param DeliveryRateFactory $deliveryRateFactory
     * @param EstimationDatesFactory $estimationDatesFactory
     * @param EstimationDatesRepository $estimationDatesRepository
     */
    public function __construct(
        Context $context,
        ShippingDate $shippingDateH,
        LoggerInterface $logger,
        DeliveryRateFactory $deliveryRateFactory,
        EstimationDatesFactory $estimationDatesFactory,
        EstimationDatesRepository $estimationDatesRepository
    ) {
        parent::__construct($context);
        $this->shippingDateH = $shippingDateH;
        $this->logger = $logger;
        $this->deliveryRateFactory = $deliveryRateFactory;
        $this->estimationDatesFactory = $estimationDatesFactory;
        $this->estimationDatesRepository = $estimationDatesRepository;
    }

    public function get($quote, $esd=null)
    {
        $esd = $esd ?: $quote->getData('ESD') ?: $this->shippingDateH->get($quote, true);
        return $this->calculateEDD($esd, $quote);
    }

    public function calculateEDD($esd, $quote, $transsmart_carrierprofile_id = false, $country = false)
    {
        $carrier_profile = $transsmart_carrierprofile_id ?: $this->getCarrierId($quote);
        $country = $country ?: $quote->getShippingAddress()->getCountryId();
        $this->logger->debug('Calculate EDD for quote:' . $quote->getId() . ', country:' . $country . ' carrier:' . $carrier_profile);
        $rate = $this->deliveryRateFactory->create()->getCollection()
        ->addFieldToFilter('country_id', $country)
        ->addFieldToFilter('carrier_id', $carrier_profile)
        ->getFirstItem();

        $weekend = str_split($rate->getWeekSchedule());

        $edd_min = $edd_max = $rate_id = null;
        if (!empty($rate) && $rate->getId()) {
            $this->logger->debug('Matched rate:' . $rate->getId() . ', ESD:' . $esd);
//            echo '<br/>min:<br/>';
            $edd_min = $this->calculateNextWorkingDay($esd, $rate->getLeadtimeMin(), $weekend);
            $this->logger->debug('Calculated next min day:' . $edd_min . ' rate-min:' . $rate->getLeadtimeMin() . ' weekend:' . $rate->getWeekSchedule());
//            echo '<br/>max:<br/>';
            $edd_max = $this->calculateNextWorkingDay($esd, $rate->getLeadtimeMax(), $weekend);
            $this->logger->debug('Calculated next max day:' . $edd_max . ' rate-max:' . $rate->getLeadtimeMax() . ' weekend:' . $rate->getWeekSchedule());
//            var_dump('ESD',$esd,'EDD_MIN',$edd_min,'ESD_MAX',$edd_max);
            $rate_id = $rate->getId();
        } else {
            $this->logger->debug('Could not find rate for country:' . $country . ', carrier_profile:' . $carrier_profile . ', quote:' . $quote->getId() . ', ESD:' . $esd);
        }

        $estimation = $this->estimationDatesFactory->create();
        $estimation->setData(
            [
                'min_delivery_date' => $edd_min,
                'max_delivery_date' => $edd_max,
                'shipping_date' => $esd,
                'carrier_id' => $carrier_profile,
                'delivery_rate_id' => $rate_id,
                'country' => $country
            ]
        );
        if ($quote instanceof \Magento\Sales\Model\Order) {
            $order = $quote;
            $estimation->addData(
                [
                'order_id' => $order->getId(),
                'order_ref' => $order->getIncrementId(),
                'order_date' => $order->getCreatedAt()
                ]
            );
            $estimation->save();
            $this->logger->debug('Orderinfo', ['order', $order->debug()]);
        }
        $this->logger->debug('Estimation', ['estimation', $estimation->getData()]);


        return $estimation;
    }

    private function calculateNextWorkingDay($from_date, $days_to_add, $weekend_schedule, $format = 'Y-m-d')
    {
        $i = $j = 1;
        $cur_date = strtotime($from_date);
        while ($i <= $days_to_add) {
            $cur_date = date('Y-m-d', $cur_date);
            $cur_date = strtotime($cur_date . ' +1 day');

            //            echo 'date working with:'.date('Y-m-d',$cur_date).'<br/>';

            $day = date('N', $cur_date);
            $day -= 1;
            //Cause we start counting from zero in weekendschedule array
            //            echo date('D', $cur_date).'<br/>';

//            echo ($weekend_schedule[$day]==1?'weekendday!':'weekday!').'<br/>';

            if ($weekend_schedule[$day] == 0) {
//                echo 'weekday'.'<br/>';
                $i++;
            } else {
//                echo 'weekendday'.'<br/>';
            }
//            echo '<hr/>';
            $j++;
//            if($j>10){break;}
        }
        //  echo sprintf('Day:%s',date('l',$dayx)) .' ';
        return date($format, $cur_date);
    }

    /*    public function getCarriersOptions($onlyValues = false)
        {
            if (!$onlyValues) {
                $options = [
                [
                    'value' => 0,
                    'label' => Mage::helper('adminhtml')->__('(empty)')
                ]
            ];
            }

            /** @var Mage_Core_Model_Resource_Db_Collection_Abstract $collection
            $collection = Mage::getModel('transsmart_shipping/carrierprofile')->getCollection()
            ->joinCarrier()
            ->joinServicelevelTime()
            ->joinServicelevelOther();
            foreach ($collection as $_model) {
                $value = $_model->getData('carrierprofile_id');
                $label = $_model->getName();
                if ($onlyValues) {
                    $options[$value] = $label;
                } else {
                    $options[] = [
                    'value' => $value,
                    'label' => $label
                ];
                }
            }

            return $options;
        }

        public function getCountriesOptions($onlyValues = false)
        {
            $collection = Mage::getModel('directory/country')->getCollection();
            $result = [];
            foreach ($collection as $country) {
                $cid = $country->getId();
                $cname = $country->getName();
                if ($onlyValues) {
                    $result[$cid] = $cname;
                } else {
                    $result[] = [
                    'value' => $cid,
                    'label' => $cname
                ];
                }
            }
            return $result;
        }*/

    public function getOptionWeekdays()
    {
        return [
        ['label' => 'Monday', 'value' => 0],
        ['label' => 'Tuesday', 'value' => 1],
        ['label' => 'Wednesday', 'value' => 2],
        ['label' => 'Thursday', 'value' => 3],
        ['label' => 'Friday', 'value' => 4],
        ['label' => 'Saturday', 'value' => 5],
        ['label' => 'Sunday', 'value' => 6]
    ];
    }

    public function getCarrierId($quote){
        if ($quote instanceof \Magento\Sales\Model\Order) {
            $shipping_method = $quote->getShippingMethod();
        } else {
            $shipping_method = $quote->getShippingAddress()->getShippingMethod();
        }
        $shipping_method_arr = explode('_', $shipping_method);
        return end($shipping_method_arr); //carrier id is always the last _ of the shipping method
    }
}
