<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace CustomConcepts\Faqs\Model\ResourceModel;

class ProdfaqsTopics extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @var \CustomConcepts\Faqs\Helper\Resource
     */
    protected $resourceHelper;

    /**
     * ProdfaqsTopics constructor.
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \CustomConcepts\Faqs\Helper\Resource $resourceHelper
     * @param null $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \CustomConcepts\Faqs\Helper\Resource $resourceHelper,
        $connectionName = null)
    {
        $this->resourceHelper = $resourceHelper;
        parent::__construct($context, $connectionName);
    }

    protected function _construct()
    {
        $this->_init('prodfaqs_topics', 'topic_id');
    }

    /**
     * @param $identifier
     * @param $storeId
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function checkIdentifier($identifier, $storeId){
        $select = $this->getConnection()->select()->from(['main_table' => $this->getMainTable()], 'topic_id')
            ->join(
                ['store_table' => $this->getTable('prodfaqs_store')],
                'main_table.topic_id = store_table.topic_id'
            )
            ->where('main_table.identifier = ?', $identifier)
            ->where('main_table.status = 1')
            ->where('store_table.store_id in (?) ', [0, $storeId])
            ->order('store_table.store_id DESC');

        return $this->getConnection()->fetchOne($select);
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return ProdfaqsTopics
     */
    public function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $prodfaqsStoreTable = $this->getTable('prodfaqs_store');
        $storeIds = $object->getData('store_id');
        $currentStoreIds = $this->resourceHelper->getTopicStores($object->getData('topic_id'));

        if($storeIds){
            //delete removed store ids
            foreach ($currentStoreIds as $currentStoreId) {
                if(!in_array($currentStoreId, $storeIds)){
                    $this->getConnection()->delete($prodfaqsStoreTable, [
                        $this->getConnection()->quoteInto('topic_id = ?', $object->getData('topic_id')),
                        $this->getConnection()->quoteInto('store_id = ?', $currentStoreId)
                    ]);
                }
            }

            //insert added store ids
            foreach ($storeIds as $storeId){
                if(!in_array($storeId, $currentStoreIds)){
                    $this->getConnection()->insert($prodfaqsStoreTable, [
                        'topic_id' => $object->getData('topic_id'),
                        'store_id' => $storeId
                    ]);
                }
            }
        }
        return parent::_afterSave($object); // TODO: Change the autogenerated stub
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return ProdfaqsTopics
     */
    public function _afterDelete(\Magento\Framework\Model\AbstractModel $object)
    {
        $prodfaqsStoreTable = $this->getTable('prodfaqs_store');
        $this->getConnection()->delete($prodfaqsStoreTable, [$this->getConnection()->quoteInto('topic_id = ?', $object->getData('topic_id'))]);

        return parent::_afterDelete($object); // TODO: Change the autogenerated stub
    }
}