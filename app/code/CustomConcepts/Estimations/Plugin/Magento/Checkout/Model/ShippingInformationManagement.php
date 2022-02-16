<?php


namespace CustomConcepts\Estimations\Plugin\Magento\Checkout\Model;

/**
 * Class ShippingInformationManagement
 *
 * @package CustomConcepts\Estimations\Plugin\Magento\Checkout\Model
 */
class ShippingInformationManagement
{

    public function afterSaveAddressInformation(
        \Magento\Checkout\Model\ShippingInformationManagement $subject,
        $result,
        $cartId,
        $addressInformation
    ) {
        //Your plugin code
        return $result;
    }
}

