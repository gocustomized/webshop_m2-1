<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace CustomConcepts\GoCustomizer\Plugin\Model;

class Cart {

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

    public function beforeAddOrderItem(
        \Magento\Checkout\Model\Cart $subject, 
        $orderItem, 
        $qtyFlag = null
    ) {
        $this->registry->register('order_item', $orderItem);
    }

}
