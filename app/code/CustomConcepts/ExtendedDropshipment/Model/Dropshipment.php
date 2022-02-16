<?php
/**
 * CustomConcepts_ExtendedDropshipment extension
 * @category  CustomConcepts_Extensions
 * @package   CustomConcepts_ExtendedDropshipment
 * @copyright Copyright (c) 2017
 * @author GoCustomized <info@gocustomized.com>
 */
namespace CustomConcepts\ExtendedDropshipment\Model;

class Dropshipment extends \Magento\Framework\Model\AbstractModel {

    /**
     * @var int
     */
    const STATUS_ENABLED = 1;

    /**
     * @var int
     */
    const STATUS_DISABLED = 0;

    /**
     * Cache tag
     * 
     * @var string
     */
    const CACHE_TAG = 'dropshipment_grid_collection';

    /**
     * Cache tag
     * 
     * @var string
     */
    protected $_cacheTag = 'dropshipment_grid_collection';

    /**
     * Event prefix
     * 
     * @var string
     */
    protected $_eventPrefix = 'dropshipment_grid_collection';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct() {
        $this->_init('CustomConcepts\ExtendedDropshipment\Model\ResourceModel\Dropshipment');
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities() {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * get entity default values
     *
     * @return array
     */
    public function getDefaultValues() {
        $values = [];

        return $values;
    }

}
