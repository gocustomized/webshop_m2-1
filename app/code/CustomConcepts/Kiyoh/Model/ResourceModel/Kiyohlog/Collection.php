<?php

/**
 * CustomConcepts_Kiyoh extension
 * @category  CustomConcepts_Extensions
 * @package   CustomConcepts_Kiyoh
 * @copyright Copyright (c) 2017
 * @author GoCustomized <info@gocustomized.com>
 */

namespace CustomConcepts\Kiyoh\Model\ResourceModel\Kiyohlog;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {

    /**
     * ID Field Name
     * 
     * @var string
     */
    protected $_idFieldName = 'id';

    /**
     * Event prefix
     * 
     * @var string
     */
    protected $_eventPrefix = 'kiyohlog_grid_collection';

    /**
     * Event object
     * 
     * @var string
     */
    protected $_eventObject = 'kiyohlogitem_grid_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct() {
        $this->_init('CustomConcepts\Kiyoh\Model\Kiyohlog', 'CustomConcepts\Kiyoh\Model\ResourceModel\Kiyohlog');
    }
}
