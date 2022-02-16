<?php
namespace CustomConcepts\Checkout\Plugin\Model;


class AfterEstimateByAddressId extends \CustomConcepts\Checkout\Plugin\Model\ExtendShippingData
{
    public function afterEstimateByAddressId(\Magento\Quote\Model\ShippingMethodManagement $shippingMethodManagement, $result){
        return $this->extendShippingData($result);
    }
}
