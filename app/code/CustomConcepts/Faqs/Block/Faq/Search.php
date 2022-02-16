<?php
namespace CustomConcepts\Faqs\Block\Faq;

class Search extends \CustomConcepts\Faqs\Block\Faq
{
    /**
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getSearchResults(){
        $term = $this->getRequest()->getParam('term');
        $searchValue = $this->getRequest()->getParam('faqssearch') ?: $term;

        $this->setData('seachValue', $searchValue);

        if($searchValue){
            $collection = $this->getProdfaqsCollection()
                ->addStoreFilter($this->_storeManager->getStore()->getId())
                ->addSearchFilter($searchValue);

            return $collection->getData();
        } else {
            return [];
        }
    }

}
