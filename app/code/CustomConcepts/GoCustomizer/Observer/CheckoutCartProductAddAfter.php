<?php

/**
 * CustomConcepts_GoCustomizer extension
 * @category  CustomConcepts_Extensions
 * @package   CustomConcepts_GoCustomizer
 * @copyright Copyright (c) 2017
 * @author GoCustomized <info@gocustomized.com>
 */

namespace CustomConcepts\GoCustomizer\Observer;

use Magento\Framework\Event\ObserverInterface;

class CheckoutCartProductAddAfter implements ObserverInterface {

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * 
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
    \Magento\Framework\Registry $registry
    ) {
        $this->registry = $registry;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {
        $orderItem = $this->registry->registry('order_item');
        $this->registry->unregister('order_item');
        $quoteItem = $observer->getEvent()->getQuoteItem();
        /* check order item having customizer data if not set to order item */
        if (!empty($orderItem) && !is_null($orderItem->getGocustomizerData())) {
            $quoteItem = $observer->getEvent()->getQuoteItem();
            $quoteItem->setGocustomizerData($orderItem->getGocustomizerData());
        }
    }

}
