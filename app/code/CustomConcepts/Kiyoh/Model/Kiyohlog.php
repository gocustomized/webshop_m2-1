<?php

/**
 * CustomConcepts_Kiyoh extension
 * @category  CustomConcepts_Extensions
 * @package   CustomConcepts_Kiyoh
 * @copyright Copyright (c) 2017
 * @author GoCustomized <info@gocustomized.com>
 */

namespace CustomConcepts\Kiyoh\Model;

class Kiyohlog extends \CustomConcepts\Kiyoh\Model\AbstractKiyohModel {

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
    const CACHE_TAG = 'kiyohlog_grid_collection';

    /**
     * Cache tag
     * 
     * @var string
     */
    protected $_cacheTag = 'kiyohlog_grid_collection';

    /**
     * Event prefix
     * 
     * @var string
     */
    protected $_eventPrefix = 'kiyohlog_grid_collection';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct() {
        $this->_init('CustomConcepts\Kiyoh\Model\ResourceModel\Kiyohlog');
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
     * Add entry in log table for any operation such as sending invitation call, adding review or stats
     * @param type $type
     * @param type $storeid
     * @param type $review
     * @param type $inivation
     * @param type $time
     * @param type $crontype
     * @param type $api_url
     * @param type $orderid
     * @return type
     */
    public function addToLog($type, $storeid, $review = '', $inivation = '', $time, $crontype = '', $api_url = '', $orderid = '') {

        if ($this->getConfigValue('kiyoh/log/enabled')) {

            $api_id = $this->getConfigValue('kiyoh/general/api_id', $storeid);
            $company = $this->getConfigValue('kiyoh/general/company', $storeid);
            $review_updates = '';
            $review_new = '';

            if ($review) {
                $review_updates = isset($review['review_updates']) ? $review['review_updates'] : '';
                $review_new = isset($review['review_new']) ? $review['review_new'] : '';
            }
            $date = new \DateTime('now');
            $model = $this->setType($type)
                    ->setShopId($api_id)
                    ->setCompany($company)
                    ->setReviewUpdate($review_updates)
                    ->setReviewNew($review_new)
                    ->setResponse($inivation)
                    ->setOrderId($orderid)
                    ->setCron($crontype)
                    ->setDate($date->format('Y-m-d H:i:s'))
                    ->setTime($time)
                    ->setApiUrl($api_url)
                    ->save();
        }

        return;
    }

}
