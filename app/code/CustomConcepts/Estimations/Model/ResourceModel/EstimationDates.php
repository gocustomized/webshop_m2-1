<?php


namespace CustomConcepts\Estimations\Model\ResourceModel;

/**
 * Class EstimationDates
 *
 * @package CustomConcepts\Estimations\Model\ResourceModel
 */
class EstimationDates extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('estimations_estimationdates', 'estimationdates_id');
    }
}

