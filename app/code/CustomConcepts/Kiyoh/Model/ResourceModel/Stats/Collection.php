<?php

/**
 * CustomConcepts_Kiyoh extension
 * @category  CustomConcepts_Extensions
 * @package   CustomConcepts_Kiyoh
 * @copyright Copyright (c) 2017
 * @author GoCustomized <info@gocustomized.com>
 */

namespace CustomConcepts\Kiyoh\Model\ResourceModel\Stats;

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
    protected $_eventPrefix = 'kiyohstat_grid_collection';

    /**
     * Event object
     * 
     * @var string
     */
    protected $_eventObject = 'kiyohstatitem_grid_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct() {
        $this->_init('CustomConcepts\Kiyoh\Model\Stats', 'CustomConcepts\Kiyoh\Model\ResourceModel\Stats');
    }
}
