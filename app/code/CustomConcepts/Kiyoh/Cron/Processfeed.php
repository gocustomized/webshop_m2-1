<?php

/**
 * CustomConcepts_Kiyoh extension
 * @category  CustomConcepts_Extensions
 * @package   CustomConcepts_Kiyoh
 * @copyright Copyright (c) 2017
 * @author GoCustomized <info@gocustomized.com>
 */

namespace CustomConcepts\Kiyoh\Cron;

class Processfeed {

    /**
     *
     * @var \CustomConcepts\Kiyoh\Model\Api
     */
    protected $api;

    /**
     *
     * @var \CustomConcepts\Kiyoh\Model\Kiyohlog
     */
    protected $kiyohLog;

    /**
     *
     * @var \CustomConcepts\Kiyoh\Model\Kiyohreview
     */
    protected $kiyohReview;

    /**
     *
     * @var \CustomConcepts\Kiyoh\Model\Stats
     */
    protected $stats;

    /**
     * 
     * @var \Magento\Config\Model\ResourceModel\Config 
     */
    protected $configModel;

    /**
     * 
     * @param \CustomConcepts\Kiyoh\Model\Api $api
     * @param \Magento\Config\Model\ResourceModel\Config $configModel
     * @param \CustomConcepts\Kiyoh\Model\Kiyohlog $kiyohLog
     * @param \CustomConcepts\Kiyoh\Model\Kiyohreview $kiyohReview
     * @param \CustomConcepts\Kiyoh\Model\Stats $stats
     */
    public function __construct(\CustomConcepts\Kiyoh\Model\Api $api, \Magento\Config\Model\ResourceModel\Config $configModel, \CustomConcepts\Kiyoh\Model\Kiyohlog $kiyohLog, \CustomConcepts\Kiyoh\Model\Kiyohreview $kiyohReview, \CustomConcepts\Kiyoh\Model\Stats $stats
    ) {
        $this->api = $api;
        $this->kiyohLog = $kiyohLog;
        $this->kiyohReview = $kiyohReview;
        $this->stats = $stats;
        $this->configModel = $configModel;
    }

    /**
     * Process cron for review stats using feed get from API 
     */
    public function processStats() {
        $storeids = $this->api->getStoreIds();

        foreach ($storeids as $storeid) {

            $enabled = $this->api->getConfigValue('kiyoh/general/enabled', $storeid);
            $cron_enabled = $this->api->getConfigValue('kiyoh/reviews/cron', $storeid);

            if ($enabled && $cron_enabled) {
                $crontype = 'stats';
                $start_time = microtime(true);
                $feed = $this->api->getFeed($storeid, $crontype);
                $resuls = array();
                $results['stats'] = $this->stats->processFeed($feed, $storeid);
                $results['company'] = $feed->company;
                $log = $this->kiyohLog->addToLog('reviews', $storeid, $results, '', (microtime(true) - $start_time), $crontype);
            }
        }
        $this->stats->processOverall();
    }

    /**
     * Process cron for review using feed get from API 
     */
    public function processReviews() {
        $storeids = $this->api->getStoreIds();
        foreach ($storeids as $storeid) {
            $enabled = $this->api->getConfigValue('kiyoh/general/enabled', $storeid);
            $cron_enabled = $this->api->getConfigValue('kiyoh/reviews/cron', $storeid);
            if ($enabled && $cron_enabled) {
                $crontype = 'reviews';
                $start_time = microtime(true);
                $feed = $this->api->getFeed($storeid, $crontype);
                $results = $this->kiyohReview->processFeed($feed, $storeid, $crontype);
                $results['stats'] = $this->stats->processFeed($feed, $storeid);
                $log = $this->kiyohLog->addToLog('reviews', $storeid, $results, '', (microtime(true) - $start_time), $crontype);
            }
        }
    }

    /**
     * Process cron for review history using feed get from API 
     */
    public function processHistory() {
        $all_storeids = $this->api->getStoreIds();
        $stores = $this->api->getConfigValue('kiyoh/general/storestoprocess');

        if ($stores) {
            $stores = json_decode($stores);
            $stores_to_process = false;
            // Kiyoh only allows to process 3 request a minute. So we have to split it up to 3 and process them all after each is done.
            if (count($stores) >= 3) {
                $stores_to_process = array_splice($stores, 0, 3);
                $this->configModel->saveConfig(
                        'kiyoh/general/storestoprocess', json_encode($stores), 'default', 0
                );
            } elseif (count($stores) >= 1) {
                $stores_to_process = array_splice($stores, 0, count($stores));
                $this->configModel->saveConfig(
                        'kiyoh/general/storestoprocess', json_encode($all_storeids), 'default', 0
                );
            } else {
                $stores_to_process = $stores = false;

                $this->configModel->saveConfig(
                        'kiyoh/general/storestoprocess', json_encode($all_storeids), 'default', 0
                );
            }
        }//Data is empty or doesn't exists yet create new one with all stores and stop.
        else {
            $this->configModel->saveConfig(
                    'kiyoh/general/storestoprocess', json_encode($all_storeids), 'default', 0
            );
            return;
        }
        $storeids = $stores_to_process;
        if ($storeids) {
            foreach ($storeids as $storeid) {
                $enabled = $this->api->getConfigValue('kiyoh/general/enabled', $storeid);
                $cron_enabled = $this->api->getConfigValue('kiyoh/reviews/cron', $storeid);
                if ($enabled && $cron_enabled) {
                    $crontype = 'history';
                    $start_time = microtime(true);
                    //$storeid = 0;
                    $feed = $this->api->getFeed($storeid, $crontype);
                    $results = $this->kiyohReview->processFeed($feed, $storeid, $crontype);
                    $results['stats'] = $this->stats->processFeed($feed, $storeid);
                    $log = $this->kiyohLog->addToLog('reviews', $storeid, $results, '', (microtime(true) - $start_time), $crontype);
                }
            }
        }
    }

    /**
     * Process cron for review stats using feed get from API 
     */
    public function cleanLog() {
        $enabled = $this->api->getConfigValue('kiyoh/log/clean', 0);
        $days = $this->api->getConfigValue('kiyoh/log/clean_days', 0);
        if (($enabled) && ($days > 0)) {
            $logmodel = $this->kiyohLog;
            $deldate = date('Y-m-d', strtotime('-' . $days . ' days'));
            $logs = $logmodel->getCollection()->addFieldToSelect('id')->addFieldToFilter('date', array('lteq' => $deldate));
            foreach ($logs as $log) {
                $logmodel->load($log->getId())->delete();
            }
        }
    }

}
