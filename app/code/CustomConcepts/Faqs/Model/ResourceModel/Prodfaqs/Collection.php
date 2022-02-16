<?php
namespace CustomConcepts\Faqs\Model\ResourceModel\Prodfaqs;

class Collection extends \CustomConcepts\Faqs\Model\ResourceModel\AbstractCollection
{
    protected function _construct()
    {
        $this->_init(\CustomConcepts\Faqs\Model\Prodfaqs::class, \CustomConcepts\Faqs\Model\ResourceModel\Prodfaqs::class);
    }

    /**
     * @param $term
     * @return $this
     */
    public function addSearchFilter($term){
        if($term){
            $this->addFieldToFilter(
                ['title', 'faq_answer'],
                [
                    ['like' => '%'.$term.'%'],
                    ['like' => '%'.$term.'%']
                ]
            );
        }

        return $this;
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
        )->group('main_table.faqs_id');
    }
}
