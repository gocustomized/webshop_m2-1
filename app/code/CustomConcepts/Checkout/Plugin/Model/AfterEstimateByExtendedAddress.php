<?php
namespace CustomConcepts\Checkout\Plugin\Model;


class AfterEstimateByExtendedAddress extends \CustomConcepts\Checkout\Plugin\Model\ExtendShippingData
{
    public function afterEstimateByExtendedAddress(\Magento\Quote\Model\ShippingMethodManagement $shippingMethodManagement, $result){
        return $this->extendShippingData($result);
    }
}
