<?php
namespace CustomConcepts\Wics\Model\ResourceModel;

class Sync extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * {@inheritdoc}
     */
    protected $_idFieldName = 'sync_id';

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('cc_wics_sync', 'sync_id');
    }
}
