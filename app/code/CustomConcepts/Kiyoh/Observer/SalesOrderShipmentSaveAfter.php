<?php

/**
 * CustomConcepts_Kiyoh extension
 * @category  CustomConcepts_Extensions
 * @package   CustomConcepts_Kiyoh
 * @copyright Copyright (c) 2017
 * @author GoCustomized <info@gocustomized.com>
 */

namespace CustomConcepts\Kiyoh\Observer;

use Magento\Framework\Event\ObserverInterface;

class SalesOrderShipmentSaveAfter implements ObserverInterface {

    /**
     * 
     * @var \CustomConcepts\Kiyoh\Model\Api $api
     */
    protected $api;

    /**
     * 
     * @param \CustomConcepts\Kiyoh\Model\Api $api
     */
    public function __construct(\CustomConcepts\Kiyoh\Model\Api $api
    ) {
        $this->api = $api;
    }

    /**
     * The function that gets executed when the event is observed
     * It sends invitation to give the review to the buyer
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer) {
        $shipment = $observer->getEvent()->getShipment();
        $order = $shipment->getOrder();
        if (($this->api->getConfigValue('kiyoh/invitation/enabled', $order->getStoreId())) && ($this->api->getConfigValue('kiyoh/general/api_key', $order->getStoreId()))):
            if ($order->getStatus() == $this->api->getConfigValue('kiyoh/invitation/status', $order->getStoreId())):
                if ($this->api->getConfigValue('kiyoh/invitation/backlog', $order->getStoreId()) > 0):
                    $date_diff = floor(time() - strtotime($order->getCreatedAt())) / (60 * 60 * 24);
                    if ($date_diff < $this->api->getConfigValue('kiyoh/invitation/backlog', $order->getStoreId())):
                        $this->api->sendInvitation($order);
                    endif;
                else:
                    $this->api->sendInvitation($order);
                endif;
            endif;
        endif;
    }

}
