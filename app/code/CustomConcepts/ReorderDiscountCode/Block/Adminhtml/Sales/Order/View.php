<?php

/**
 * CustomConcepts_ReorderDiscountCode extension
 * @category  CustomConcepts_Extensions
 * @package   CustomConcepts_ReorderDiscountCode
 * @copyright Copyright (c) 2017
 * @author GoCustomized <info@gocustomized.com>
 */

namespace CustomConcepts\ReorderDiscountCode\Block\Adminhtml\Sales\Order;

class View extends \Magento\Backend\Block\Widget\Form\Container {

    /**
     * 
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param array $data
     */
    public function __construct(
    \Magento\Backend\Block\Widget\Context $context, array $data = array()
    ) {
        parent::__construct($context, $data);
    }

    public function _construct() {

        $this->_objectId = 'order_id';
        $this->_controller = 'adminhtml_order';
        $this->_mode = 'view';
        $this->_blockGroup = 'Magento_Sales';

        parent::_construct();
        
        //add button on order view page
        $this->buttonList->add(
                'discountcode', [
            'label' => 'Sent discount code to client',
            'id' => 'discountcode',
                ]
        );
    }

}
