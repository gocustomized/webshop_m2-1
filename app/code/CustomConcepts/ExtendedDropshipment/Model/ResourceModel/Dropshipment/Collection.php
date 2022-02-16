<?php

/**
 * CustomConcepts_ExtendedDropshipment extension
 * @category  CustomConcepts_Extensions
 * @package   CustomConcepts_ExtendedDropshipment
 * @copyright Copyright (c) 2017
 * @author GoCustomized <info@gocustomized.com>
 */

namespace CustomConcepts\ExtendedDropshipment\Model\ResourceModel\Dropshipment;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {

    /**
     * ID Field Name
     * 
     * @var string
     */
    protected $_idFieldName = 'item_id';

    /**
     * Event prefix
     * 
     * @var string
     */
    protected $_eventPrefix = 'dropshipment_grid_collection';

    /**
     * Event object
     * 
     * @var string
     */
    protected $_eventObject = 'dropshipmentitem_grid_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct() {
        $this->_init('CustomConcepts\ExtendedDropshipment\Model\Dropshipment', 'CustomConcepts\ExtendedDropshipment\Model\ResourceModel\Dropshipment');
    }

    protected function _renderFiltersBefore() {
        $wherePart = $this->getSelect()->getPart(\Magento\Framework\DB\Select::WHERE);

        foreach ($wherePart as $key => $cond) {
            if (strpos($cond, 'sku')) {
                $wherePart[$key] = str_replace('`sku`', "main_table.sku", $cond);
            }
            if (strpos($cond, 'dropshipment_id')) {
                $wherePart[$key] = str_replace('`dropshipment_id`', "CONCAT(dropshiporder.increment_id,  main_table.item_id)", $cond);
            }
            if (strpos($cond, 'increment_id')) {
                $wherePart[$key] = str_replace('`increment_id`', "dropshiporder.increment_id", $cond);
            }
            if (strpos($cond, 'customer_id')) {
                $wherePart[$key] = str_replace('`customer_id`', "dropshiporder.customer_id", $cond);
            }
            if (strpos($cond, 'supplier')) {
                $wherePart[$key] = str_replace('`supplier`', "option.value", $cond);
            }
            if (strpos($cond, 'item_status')) {
                $wherePart[$key] = str_replace('`item_status`', "dropshiporder.status", $cond);
            }
        }
        $this->getSelect()->setPart(\Magento\Framework\DB\Select::WHERE, $wherePart);
    }

}
