<?php
namespace CustomConcepts\Checkout\Plugin\Model;

abstract class ExtendShippingData
{
    /**
     * @var \CustomConcepts\Estimations\Helper\Data
     */
    protected $estimationsHelper;
    /**
     * @var \CustomConcepts\Checkout\Model\Config\Source\TrackingMethod
     */
    protected $trackingMethod;

    protected $date;

    protected $carrierRepository;

    protected $profileRepository;

    /**
     * ExtendShippingData constructor.
     * @param \CustomConcepts\Estimations\Helper\Data $estimationsHelper
     * @param \CustomConcepts\Checkout\Model\Config\Source\TrackingMethod $trackingMethod
     */
    public function __construct(
        \CustomConcepts\Estimations\Helper\Data $estimationsHelper,
        \CustomConcepts\Checkout\Model\Config\Source\TrackingMethod $trackingMethod,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date,
        \Bluebirdday\TranssmartSmartConnect\Model\CarrierRepository $carrierRepository,
        \Bluebirdday\TranssmartSmartConnect\Model\Booking\ProfileRepository $profileRepository
    ){
        $this->estimationsHelper = $estimationsHelper;
        $this->trackingMethod = $trackingMethod;
        $this->date = $date;
        $this->carrierRepository = $carrierRepository;
        $this->profileRepository = $profileRepository;
    }

    /**
     * @param $result
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function extendShippingData($result){
        if($result){
            $extendedData = [];
            foreach ($result as $shippingMethod){
                $methodCode = $shippingMethod->getMethodCode();
                $methodCodeArr = explode("_", $methodCode); //[0] => method, ['1'] => id

                if(isset($methodCodeArr[1])){
                    $edd = $this->estimationsHelper->getEdd($methodCodeArr[1]);
                    $shippingInfo = $this->estimationsHelper->getShippingMethodConfig($methodCodeArr[1]);

                    $trackingMethodValue = $this->estimationsHelper->getShippingInfo($shippingInfo, 'tracking_method');
//                    $trackingMethodLabel = !empty($trackingMethodValue) ? $this->trackingMethod->getLabel($trackingMethodValue) : '';
                    $shippingDesc = $this->estimationsHelper->getShippingInfo($shippingInfo, 'textarea');

                    if($moneybackGuarantee = $this->estimationsHelper->getShippingInfo($shippingInfo, 'moneyback_guarantee')){
//                        $shiptext = '<span class="bold-green">'.__('moneyback_guarantee').': </span>';
                        $shiptext = 'moneyback_guarantee';
                    }else{
                        $shiptext = 'Estimated delivery';
                    }

                    $formattedMin = $edd['min_delivery_date'] ? $this->date->formatDate($edd['min_delivery_date'], \IntlDateFormatter::FULL, false) : '';
                    $formattedMax = $edd['max_delivery_date'] ? $this->date->formatDate($edd['max_delivery_date'], \IntlDateFormatter::FULL, false) : '';
                    if($edd['min_delivery_date'] == $edd['max_delivery_date']){
                        $eddDateString = $formattedMin;
                    } else {
                        $eddDateString = $formattedMin . ' - '  . $formattedMax;
                    }

                    $extendedData[$methodCode] = [
                        'edd' => $eddDateString,
                        'shiptext' => $shiptext,
                        'moneyback_guarantee' => $moneybackGuarantee,
                        'tracking_method' => $trackingMethodValue,
                        'shipping_description' => $shippingDesc,
                        'home_delivery' => $shippingDesc = $this->estimationsHelper->getShippingInfo($shippingInfo, 'home_delivery'),
                        'location_select' => $this->getLocationSelect($methodCodeArr[1])
                    ];
                }
            }

            $result[]['extended_data'] = $extendedData;
        }

        return $result;
    }

    private function getLocationSelect($profileCode){
        $profile = $this->profileRepository->loadByCode($profileCode);
        return $this->carrierRepository->load($profile->getCarrierId())->getLocationSelect();
    }
}
