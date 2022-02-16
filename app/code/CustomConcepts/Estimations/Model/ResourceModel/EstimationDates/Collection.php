<?php


namespace CustomConcepts\Estimations\Model\ResourceModel\EstimationDates;

/**
 * Class Collection
 *
 * @package CustomConcepts\Estimations\Model\ResourceModel\EstimationDates
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * @var string
     */
    protected $_idFieldName = 'estimationdates_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \CustomConcepts\Estimations\Model\EstimationDates::class,
            \CustomConcepts\Estimations\Model\ResourceModel\EstimationDates::class
        );
    }
}

