<?php

/**
 * CustomConcepts_Kiyoh extension
 * @category  CustomConcepts_Extensions
 * @package   CustomConcepts_Kiyoh
 * @copyright Copyright (c) 2017
 * @author GoCustomized <info@gocustomized.com>
 */

namespace CustomConcepts\Kiyoh\Model\ResourceModel;

class Stats extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {

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
        $this->_init('kiyoh_stats', 'id');
    }

}
