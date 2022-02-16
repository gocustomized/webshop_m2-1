<?php
namespace CustomConcepts\Faqs\Model\ResourceModel\ProdfaqsTopics;

class Collection extends \CustomConcepts\Faqs\Model\ResourceModel\AbstractCollection
{
    protected function _construct()
    {
        $this->_init(\CustomConcepts\Faqs\Model\ProdfaqsTopics::class, \CustomConcepts\Faqs\Model\ResourceModel\ProdfaqsTopics::class);
    }

    public function _initSelect()
    {
        parent::_initSelect();
        $prodfaqsStore = $this->getConnection()->getTableName('prodfaqs_store');
        $this->joinStoreRelationTable($prodfaqsStore, 'topic_id');
        $this->addFilterToMap('topic_id', 'main_table.topic_id');

    }

    protected function joinStoreRelationTable($tableName, $linkField)
    {
        $this->getSelect()->join(
            ['store_table' => $this->getTable($tableName)],
            'main_table.' . $linkField . ' = store_table.' . $linkField,
            ['store_id']//            array('store_id' => 'store_id', 'ttopic_id' => 'topic_id')
        )->group('main_table.topic_id');
    }
}
