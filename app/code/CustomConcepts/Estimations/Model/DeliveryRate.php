<?php
/**
 * Copyright © 2015 CustomConcepts. All rights reserved.
 */

namespace CustomConcepts\Estimations\Model;

use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;

/**
 * DeliveryRatetab deliveryrate model
 */
class DeliveryRate extends \Magento\Framework\Model\AbstractModel
{

    /**
     * @param Context $context
     * @param Registry $registry
     * @param AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * @return void
     */
    public function _construct()
    {
        $this->_init('CustomConcepts\Estimations\Model\ResourceModel\DeliveryRate');
    }
}
