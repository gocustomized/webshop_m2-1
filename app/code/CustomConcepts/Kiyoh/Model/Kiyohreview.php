<?php

/**
 * CustomConcepts_Kiyoh extension
 * @category  CustomConcepts_Extensions
 * @package   CustomConcepts_Kiyoh
 * @copyright Copyright (c) 2017
 * @author GoCustomized <info@gocustomized.com>
 */

namespace CustomConcepts\Kiyoh\Model;

class Kiyohreview extends \CustomConcepts\Kiyoh\Model\AbstractKiyohModel {

    /**
     * @var int
     */
    const STATUS_ENABLED = 1;

    /**
     * @var int
     */
    const STATUS_DISABLED = 0;

    /**
     * Cache tag
     * 
     * @var string
     */
    const CACHE_TAG = 'kiyohreview_grid_collection';

    /**
     * Cache tag
     * 
     * @var string
     */
    protected $_cacheTag = 'kiyohreview_grid_collection';

    /**
     * Event prefix
     * 
     * @var string
     */
    protected $_eventPrefix = 'kiyohreview_grid_collection';

    /**
     * 
     * @var \Magento\Config\Model\ResourceModel\Config 
     */
    protected $configModel;
    /**
     * 
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Config\Model\ResourceModel\Config $configModel
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(\Magento\Framework\Model\Context $context, \Magento\Config\Model\ResourceModel\Config $configModel, \Magento\Framework\Registry $registry, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null, \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null, array $data = array()) {
        $this->configModel = $configModel;
        parent::__construct($context, $registry, $scopeConfig, $storeManager, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct() {
        $this->_init('CustomConcepts\Kiyoh\Model\ResourceModel\Kiyohreview');
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities() {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * get entity default values
     *
     * @return array
     */
    public function getDefaultValues() {
        $values = [];

        return $values;
    }

    public function loadbyKiyohId($kiyoh_id) {
        $review = $this->load($kiyoh_id, 'kiyoh_id');
        return $review;
    }

    /**
     * Insert reviews into table which is coming from API feed
     * @param type $feed
     * @param type $storeid
     * @param type $type
     * @return type
     */
    public function processFeed($feed, $storeid = 0, $type) {

        $updates = 0;
        $new = 0;
        $history = 0;
        $api_id = $this->getConfigValue('kiyoh/general/api_id', $storeid);
        $company = $feed->company->name;

        foreach ($feed->review_list->review as $review) {

            $kiyoh_id = $review->id;
            $customer_name = $review->customer->name;
            $customer_email = $review->customer->email;
            $customer_place = $review->customer->place;
            $date = $review->customer->date;
            $total_score = $review->total_score;

            $recommendation = $review->recommendation;
            $positive = $review->positive;
            $negative = $review->negative;
            $purchase = $review->purchase;
            $reaction = $review->reaction;

            if (($recommendation == 'Ja') || ($recommendation == 'Yes')) {
                $recommendation = 1;
            } else {
                $recommendation = 0;
            }

            $questions = array();
            foreach ($review->questions->question as $question) {
                $questions[] = $question->score;
            }

            $indatabase = $this->loadbyKiyohId($kiyoh_id);

            if ($indatabase->getReviewId()) {
                if ($type == 'history') {
                    $reviews = $this->setReviewId($indatabase->getReviewId())
                            ->setShopId($api_id)
                            ->setCompany($company)
                            ->setKiyohId($kiyoh_id)
                            ->setCustomerName($customer_name)
                            ->setCustomerEmail($customer_email)
                            ->setCustomerPlace($customer_place)
                            ->setScore($total_score)
                            ->setScoreQ2($questions[0])
                            ->setScoreQ3($questions[1])
                            ->setScoreQ4($questions[2])
                            ->setScoreQ5($questions[3])
                            ->setScoreQ6($questions[4])
                            ->setScoreQ7($questions[5])
                            ->setScoreQ8($questions[6])
                            ->setScoreQ9($questions[7])
                            ->setScoreQ10($questions[8])
                            ->setRecommendation($recommendation)
                            ->setPositive($positive)
                            ->setNegative($negative)
                            ->setPurchase($purchase)
                            ->setReaction($reaction)
                            ->setDateCreated($date)
                            ->save();
                    $updates++;
                } else {
                    break;
                }
            } else {
                $reviews = $this->setShopId($api_id)
                        ->setCompany($company)
                        ->setKiyohId($kiyoh_id)
                        ->setCustomerName($customer_name)
                        ->setCustomerEmail($customer_email)
                        ->setCustomerPlace($customer_place)
                        ->setScore($total_score)
                        ->setScoreQ2($questions[0])
                        ->setScoreQ3($questions[1])
                        ->setScoreQ4($questions[2])
                        ->setScoreQ5($questions[3])
                        ->setScoreQ6($questions[4])
                        ->setScoreQ7($questions[5])
                        ->setScoreQ8($questions[6])
                        ->setScoreQ9($questions[7])
                        ->setScoreQ10($questions[8])
                        ->setRecommendation($recommendation)
                        ->setPositive($positive)
                        ->setNegative($negative)
                        ->setPurchase($purchase)
                        ->setReaction($reaction)
                        ->setDateCreated($date)
                        ->save();
                $new++;
            }
        }
        $date = new \DateTime('now');

        $config = $this->configModel->saveConfig(
                'kiyoh/reviews/lastrun', $date->format('Y-m-d H:i:s'), 'default', 0
        );

        $result = array();
        $result['review_updates'] = $updates;
        $result['review_new'] = $new;
        $result['company'] = $company;
        return $result;
    }

}
