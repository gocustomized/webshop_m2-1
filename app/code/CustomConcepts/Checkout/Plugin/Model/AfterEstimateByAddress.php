<?php
namespace CustomConcepts\Checkout\Plugin\Model;


class AfterEstimateByAddress extends \CustomConcepts\Checkout\Plugin\Model\ExtendShippingData
{
    public function afterEstimateByAddress(\Magento\Quote\Model\ShippingMethodManagement $shippingMethodManagement, $result){
        return $this->extendShippingData($result);
    }
}
