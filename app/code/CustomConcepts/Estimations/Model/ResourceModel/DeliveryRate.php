<?php
/**
 * Copyright © 2015 CustomConcepts. All rights reserved.
 */
namespace CustomConcepts\Estimations\Model\ResourceModel;

/**
 * DeliveryRate resource
 */
class DeliveryRate extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('estimations_deliveryrate', 'id');
    }

  
}
