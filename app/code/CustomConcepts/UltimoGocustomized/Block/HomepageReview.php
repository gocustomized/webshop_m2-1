<?php
namespace CustomConcepts\UltimoGocustomized\Block;

class HomepageReview extends \Magento\Catalog\Block\Product\AbstractProduct
{
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timeZone,
        \CustomConcepts\Kiyoh\Model\ResourceModel\Kiyohreview\Collection $ReviewCollection,
        \CustomConcepts\Kiyoh\Model\Stats $stateFactory,
        \CustomConcepts\UltimoGocustomized\Helper\Data $helper,
        array $data = array()
    ) {
        $this->reviews = $ReviewCollection;
        $this->helper = $helper;
        $this->stateFactory = $stateFactory;
        $this->timeZone = $timeZone;
        $this->storeManager = $context->getStoreManager();
        parent::__construct($context, $data);
    }
    public function getApiId(){
        return $this->helper->getConfig('kiyoh/general/api_id');
    }
    public function dateFormater() {
        return $this->timeZone;
    }
    public function getReviews($limit) {
        $reviews = $this->reviews;
        
        $reviews->setOrder('date_created', 'DESC');
        $reviews->addFieldToFilter('status', 1);
        $reviews->addFieldToFilter('score', array('gt' => '7'));
        $reviews->addFieldToFilter('positive', array('neq' => NULL));
        $reviews->addFieldToFilter('shop_id', $this->getApiId());
        $reviews->getSelect()->limit($limit);
        
        return $reviews;
    }
    public function getStats() {
        return $this->stateFactory->loadbyShopId($this->getApiId());
    }
    public function getReviewPageUrl() {
        return $this->storeManager->getStore()->getBaseUrl().'reviews';
    }
}