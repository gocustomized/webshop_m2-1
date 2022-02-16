<?php

/**
 * CustomConcepts_ExtendedDropshipment extension
 * @category  CustomConcepts_Extensions
 * @package   CustomConcepts_ExtendedDropshipment
 * @copyright Copyright (c) 2017
 * @author GoCustomized <info@gocustomized.com>
 */

namespace CustomConcepts\ExtendedDropshipment\Model\ResourceModel;

class Dropshipment extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {

    /**
     * constructor
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     */
    public function __construct(\Magento\Framework\Model\ResourceModel\Db\Context $context
    ) {
        parent::__construct($context);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct() {
        $this->_init('sales_order_item', 'item_id');
    }

}
