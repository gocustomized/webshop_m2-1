<?php
namespace CustomConcepts\Checkout\Plugin\Model;


class AfterGetEstimatedRates extends \CustomConcepts\Checkout\Plugin\Model\ExtendShippingData
{
    public function afterGetEstimatedRates(\Magento\Quote\Model\ShippingMethodManagement $shippingMethodManagement, $result){
        return $this->extendShippingData($result);
    }
}
