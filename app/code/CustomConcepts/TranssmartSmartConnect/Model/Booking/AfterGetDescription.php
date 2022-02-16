<?php
namespace CustomConcepts\TranssmartSmartConnect\Model\Booking;


class AfterGetDescription
{
    protected $estimationsHelper;

    public function __construct(
        \CustomConcepts\Estimations\Helper\Data $estimationsHelper
    ){
        $this->estimationsHelper = $estimationsHelper;
    }

    public function afterGetDescription(\Bluebirdday\TranssmartSmartConnect\Model\Booking\Profile $subject, $result){

        $shippingMethodConfig = $this->estimationsHelper->getShippingMethodConfig($subject->getCode());
        $title = $this->estimationsHelper->getShippingInfo($shippingMethodConfig, 'title');
        if($title){
            return $title;
        }

        return $result;
    }
}
