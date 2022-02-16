<?php
namespace CustomConcepts\Faqs\Block\Faq;

class Topics extends \CustomConcepts\Faqs\Block\Faq
{
    /**
     * @param null $id
     * @return array|mixed|null
     */
    public function getTopics($id = null)
    {
        $collection = $this->getTopicsCollections();

        if($id){
            $collection->addFieldToFilter('main_table.topic_order',$id);
        }

        if (!$this->hasData('topic')) {
            $this->setData('topic', $collection);
        }
        return $this->getData('topic');
    }

    /**
     * @param $topicId
     * @return mixed
     */
    public function getFaqsOfMainTopics($topicId){
        $collection = $this->getProdfaqsCollection()
            ->addFieldToFilter('topic_id',$topicId)
            ->addFieldToFilter('main_table.show_on_main',1)
            ->addFieldToFilter('main_table.parent_faq_id',0);

        return $collection->getData();
    }

    /**
     * @param int $limit
     * @return mixed
     */
    public function getMainTopics($limit = 5)  {
        return $this->getTopicsCollections()->setPageSize($limit);
    }

    /**
     * @param int $limit
     * @return mixed
     */
    public function getSideTopics($limit = 5)  {
        $_topicsCollection = $this->getMainTopics();
        $_topics = [];
        foreach ($_topicsCollection as $_topic) {
            $_topic->setData('current', ($_topic->getId() == $this->getRequest()->getParam('id', null)));
            $_topics[] = $_topic->getData();
        }
        usort($_topics, function ($a, $b) { return strnatcmp($b['current'], $a['current']); });
        return $_topics;
    }

    /**
     * @return mixed
     */
    public function getShowNumberOfQuestions(){
        return $this->prodfaqsHelper->getShowNumberOfQuestions();
    }

    /**
     * @param $topicId
     * @return bool
     */
    public function isCurrentTopic($topicId){
        $currentTopicId = $this->getRequest()->getParam('id', null);
        $sessionTopicId = $this->_session->getTopicId();

        if ($sessionTopicId != $currentTopicId) {
            $this->_session->unsTopicId();
            $this->_session->setTopicId($currentTopicId);
        }
                
        if(!empty($sessionTopicId)){
            return $sessionTopicId == $topicId;
        }
    }
}

