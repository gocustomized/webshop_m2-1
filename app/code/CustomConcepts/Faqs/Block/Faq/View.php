<?php
namespace CustomConcepts\Faqs\Block\Faq;

use CustomConcepts\Faqs\Block\Faq;

class View extends Faq
{
    /**
     * @return |null
     */
    public function viewTopicFaqs(){
        $topicId = $this->getRequest()->getParam('id', null);

        if(is_numeric($topicId)){
            $collection = $this->getProdfaqsCollection()
                ->addFieldToFilter('topic_id', $topicId)
                ->addFieldToFilter('parent_faq_id', 0);

            return $collection->getData();
        } else {
            return null;
        }
    }

    /**
     * @return mixed
     */
    public function getTopicTitle(){
        $topicId = $this->getRequest()->getParam('id', null);

        $prodfaqsTopic = $this->prodfaqsTopicsFactory->create()->load($topicId);

        return $prodfaqsTopic->getTitle();
    }



}
