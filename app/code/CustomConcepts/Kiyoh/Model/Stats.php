<?php

/**
 * CustomConcepts_Kiyoh extension
 * @category  CustomConcepts_Extensions
 * @package   CustomConcepts_Kiyoh
 * @copyright Copyright (c) 2017
 * @author GoCustomized <info@gocustomized.com>
 */

namespace CustomConcepts\Kiyoh\Model;

class Stats extends \CustomConcepts\Kiyoh\Model\AbstractKiyohModel {

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
    const CACHE_TAG = 'kiyohstat_grid_collection';

    /**
     * Cache tag
     * 
     * @var string
     */
    protected $_cacheTag = 'kiyohstat_grid_collection';

    /**
     * Event prefix
     * 
     * @var string
     */
    protected $_eventPrefix = 'kiyohstat_grid_collection';

    /**
     * 
     * @var \Magento\Config\Model\ResourceModel\Config 
     */
    protected $configModel;

    /**
     * 
     * @var \CustomConcepts\Kiyoh\Model\ResourceModel\Stats\CollectionFactor
     */
    protected $collectionFactory;
    /**
     * 
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Config\Model\ResourceModel\Config $configModel
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \CustomConcepts\Kiyoh\Model\ResourceModel\Stats\CollectionFactory $collectionFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(\Magento\Framework\Model\Context $context, \Magento\Config\Model\ResourceModel\Config $configModel, \Magento\Framework\Registry $registry, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Magento\Store\Model\StoreManagerInterface $storeManager, \CustomConcepts\Kiyoh\Model\ResourceModel\Stats\CollectionFactory $collectionFactory, \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null, \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null, array $data = array()) {
        $this->configModel = $configModel;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context, $registry, $scopeConfig, $storeManager, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct() {
        $this->_init('CustomConcepts\Kiyoh\Model\ResourceModel\Stats');
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
    /**
     * Process feed and store data into stats
     * @param type $feed
     * @param type $storeid
     * @return boolean
     */
    public function processFeed($feed, $storeid = 0) {
        $shop_id = $this->getConfigValue('kiyoh/general/api_id', $storeid);
        $company = $feed->company->name;

        if ($storeid == 0) {
            $this->configModel->saveConfig('kiyoh/general/url', $feed->company->url, 'default', $storeid);
            $this->configModel->saveConfig('kiyoh/general/company', $feed->company->name, 'default', $storeid);
        } else {
            $this->configModel->saveConfig('kiyoh/general/url', $feed->company->url, 'stores', $storeid);
            if (!$this->getConfigValue('kiyoh/general/url', 0)) {
                $this->configModel->saveConfig('kiyoh/general/url', $feed->company->url, 'default', 0);
            }
            if (!$this->getConfigValue('kiyoh/general/company', 0)) {
                $this->configModel->saveConfig('kiyoh/general/company', $feed->company->name, 'default', 0);
            }
        }

        if ($feed->company->total_reviews > 0) {
            $score = floatval($feed->company->total_score);
            $score = ($score * 10);
            $scoremax = '100';
            $votes = $feed->company->total_reviews;

            // Check for update or save
            if ($indatabase = $this->loadbyShopId($shop_id)) {
                $id = $indatabase->getId();
            } else {
                $id = '';
            }

            $questions = array();
            foreach ($feed->company->average_scores->questions->question as $question) {
                $questions[] = array('title' => $question->title, 'score' => $question->score);
            }

            // Save Review Stats
            $model = $this->setId($id)
                    ->setShopId($shop_id)
                    ->setCompany($company)
                    ->setScore($score)
                    ->setScoreQ2($questions[0]['score'])
                    ->setScoreQ2Title($questions[0]['title'])
                    ->setScoreQ3($questions[1]['score'])
                    ->setScoreQ3Title($questions[1]['title'])
                    ->setScoreQ4($questions[2]['score'])
                    ->setScoreQ4Title($questions[2]['title'])
                    ->setScoreQ5($questions[3]['score'])
                    ->setScoreQ5Title($questions[3]['title'])
                    ->setScoreQ6($questions[4]['score'])
                    ->setScoreQ6Title($questions[4]['title'])
                    ->setScoreQ7($questions[5]['score'])
                    ->setScoreQ7Title($questions[5]['title'])
                    ->setScoreQ8($questions[6]['score'])
                    ->setScoreQ8Title($questions[6]['title'])
                    ->setScoreQ9($questions[7]['score'])
                    ->setScoreQ9Title($questions[7]['title'])
                    ->setScoreQ10($questions[8]['score'])
                    ->setScoreQ10Title($questions[8]['title'])
                    ->setScoremax($scoremax)
                    ->setVotes($votes)
                    ->save();
            return true;
        } else {
            return false;
        }
    }
    /**
     * Process overall score
     */
    public function processOverall() {
        $stats =  $this->collectionFactory->create();
        $stats->addFieldToFilter('shop_id', array('neq' => '0'));

        $score = '';
        $scoremax = '';
        $votes = '';
        $i = 0;

        foreach ($stats as $stat) {
            $score = ($score + $stat->getScore());
            $scoremax = ($scoremax + $stat->getScoremax());
            $votes = ($votes + $stat->getVotes());
            $i++;
        }

        $score = ($score / $i);
        $scoremax = ($scoremax / $i);
        $company = 'Overall';

        if ($indatabase = $this->loadbyShopId(0)) {
            $id = $indatabase->getId();
        } else {
            $id = '';
        }

        $model = $this
                ->setId($id)
                ->setShopId(0)
                ->setCompany($company)
                ->setScore($score)
                ->setScoremax($scoremax)
                ->setVotes($votes)
                ->save();
    }

    public function loadbyShopId($shop_id) {
        $this->_getResource()->load($this, $shop_id, 'shop_id');
        return $this;
    }

}
