<?php
namespace CustomConcepts\Wics\Model\ResourceModel\Sync;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(
            \CustomConcepts\Wics\Model\Sync::class,
            \CustomConcepts\Wics\Model\ResourceModel\Sync::class
        );
    }
}
