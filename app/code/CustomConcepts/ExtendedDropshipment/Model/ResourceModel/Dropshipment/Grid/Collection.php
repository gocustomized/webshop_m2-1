<?php

/**
 * CustomConcepts_ExtendedDropshipment extension
 * @category  CustomConcepts_Extensions
 * @package   CustomConcepts_ExtendedDropshipment
 * @copyright Copyright (c) 2017
 * @author GoCustomized <info@gocustomized.com>
 */

namespace CustomConcepts\ExtendedDropshipment\Model\ResourceModel\Dropshipment\Grid;

class Collection extends \CustomConcepts\ExtendedDropshipment\Model\ResourceModel\Dropshipment\Collection implements \Magento\Framework\Api\Search\SearchResultInterface {

    /**
     * Aggregations
     * 
     * @var \Magento\Framework\Search\AggregationInterface
     */
    protected $_aggregations;

    /**
     * constructor
     * 
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param $mainTable
     * @param $eventPrefix
     * @param $eventObject
     * @param $resourceModel
     * @param $model
     * @param $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource
     */
    public function __construct(
    \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory, \Psr\Log\LoggerInterface $logger, \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy, \Magento\Framework\Event\ManagerInterface $eventManager, $mainTable, $eventPrefix, $eventObject, $resourceModel, $model = 'Magento\Framework\View\Element\UiComponent\DataProvider\Document', \Magento\Framework\DB\Adapter\AdapterInterface $connection = null, \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->_eventPrefix = $eventPrefix;
        $this->_eventObject = $eventObject;
        $this->_init($model, $resourceModel);
        $this->setMainTable($mainTable);
    }

    protected function _initSelect() {
        parent::_initSelect();

        $this->getSelect()->joinInner(
                array('dropshiporder' => 'sales_order'), 'main_table.order_id=dropshiporder.entity_id', ['dropshipment_id' => "CONCAT(dropshiporder.increment_id,  main_table.item_id)", 'increment_id' => 'increment_id','api_order_id' => 'api_order_id', 'store_id', 'customer_group_id', 'customer_id', 'status', 'item_status'=> 'status']);
        //join for supplier
        $this->getSelect()->joinLeft(
                array('option' => 'eav_attribute_option_value'), 'main_table.supplier_id=`option`.option_id', ['supplier' => 'option.value']);
        $this->getSelect()->joinLeft(
                array('shippeditem' => 'sales_shipment_item'), 'main_table.item_id=`shippeditem`.order_item_id', ['parent' => 'parent_id']);
        $this->getSelect()->joinLeft(
                array('shipment' => 'sales_shipment'), 'shippeditem.parent_id=`shipment`.entity_id', ['tracking_url' => 'transsmart_tracking_url']);
        $this->getSelect()->columns("CONCAT(dropshiporder.increment_id,  main_table.item_id) as dropshipment_id");

        $this->getSelect()->group('main_table.item_id');
    }

    /**
     * @return \Magento\Framework\Search\AggregationInterface
     */
    public function getAggregations() {
        return $this->_aggregations;
    }

    /**
     * @param \Magento\Framework\Search\AggregationInterface $aggregations
     * @return $this
     */
    public function setAggregations($aggregations) {
        $this->_aggregations = $aggregations;
    }

    /**
     * Retrieve all ids for collection
     * Backward compatibility with EAV collection
     *
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getAllIds($limit = null, $offset = null) {
        return $this->getConnection()->fetchCol($this->_getAllIdsSelect($limit, $offset), $this->_bindParams);
    }

    /**
     * Get search criteria.
     *
     * @return \Magento\Framework\Api\SearchCriteriaInterface|null
     */
    public function getSearchCriteria() {
        return null;
    }

    /**
     * Set search criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setSearchCriteria(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null) {
        return $this;
    }

    /**
     * Get total count.
     *
     * @return int
     */
    public function getTotalCount() {
        return $this->getSize();
    }

    /**
     * Set total count.
     *
     * @param int $totalCount
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setTotalCount($totalCount) {
        return $this;
    }

    /**
     * Set items list.
     *
     * @param \Magento\Framework\Api\ExtensibleDataInterface[] $items
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setItems(array $items = null) {
        return $this;
    }

}
