<?php

/**
 * CustomConcepts_Kiyoh extension
 * @category  CustomConcepts_Extensions
 * @package   CustomConcepts_Kiyoh
 * @copyright Copyright (c) 2017
 * @author GoCustomized <info@gocustomized.com>
 */

namespace CustomConcepts\Kiyoh\Model;

class Api extends \CustomConcepts\Kiyoh\Model\AbstractKiyohModel {

    /**
     *
     * @var \CustomConcepts\Kiyoh\Model\Kiyohreview  
     */
    protected $kiyohReview;

    /**
     *
     * @var \CustomConcepts\Kiyoh\Model\Kiyohlog 
     */
    protected $kiyohLog;

    /**
     *
     * @var \CustomConcepts\Kiyoh\Model\Stats 
     */
    protected $kiyohStats;

    /**
     * Scope config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     *
     * @var type \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     *
     * @var type \Magento\Framework\HTTP\Client\Curl
     */
    protected $curl;

    /**
     *
     * @var type \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * 
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\HTTP\Client\Curl $curl
     * @param \CustomConcepts\Kiyoh\Model\Kiyohreview $kiyohReview
     * @param \CustomConcepts\Kiyoh\Model\Kiyohlog $kiyohLog
     * @param \CustomConcepts\Kiyoh\Model\Stats $kiyohStats
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(\Magento\Framework\Model\Context $context, \Magento\Framework\HTTP\Client\Curl $curl, \CustomConcepts\Kiyoh\Model\Kiyohreview $kiyohReview, \CustomConcepts\Kiyoh\Model\Kiyohlog $kiyohLog, \CustomConcepts\Kiyoh\Model\Stats $kiyohStats, \Magento\Framework\Registry $registry, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null, \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null, array $data = array()) {
        $this->kiyohReview = $kiyohReview;
        $this->kiyohLog = $kiyohLog;
        $this->kiyohStats = $kiyohStats;
        $this->curl = $curl;
        $this->logger = $context->getLogger();
        $this->storeManager = $storeManager;
        parent::__construct($context, $registry, $scopeConfig, $storeManager, $resource, $resourceCollection, $data);
    }

    /**
     * Process the feed
     * @param type $storeid
     * @param type $type
     * @return boolean
     */
    public function processFeed($storeid = 0, $type) {
        if ($feed = $this->getFeed($storeid, $type)) {
            $results = $this->kiyohReview->processFeed($feed, $storeid, $type);
            $results['stats'] = $this->kiyohStats->processFeed($feed, $storeid);
            return $results;
        } else {
            return false;
        }
    }

    /**
     * Get the feed from API for review and enter data into our system
     * @param type $storeid
     * @param type $type
     * @return boolean
     */
    public function getFeed($storeid, $type = '') {
        $api_id = $this->getConfigValue('kiyoh/general/api_id', $storeid);
        $api_key = $this->getConfigValue('kiyoh/general/api_key', $storeid);
        $api_url = $this->getConfigValue('kiyoh/general/api_url', $storeid);

        if ($type == 'stats') {
            $api_url = 'https://' . $api_url . '/xml/recent_company_reviews.xml?connectorcode=' . $api_key . '&company_id=' . $api_id . '&reviewcount=10';
        }
        if ($type == 'reviews') {
            $api_url = 'https://' . $api_url . '/xml/recent_company_reviews.xml?connectorcode=' . $api_key . '&company_id=' . $api_id . '&reviewcount=10';
        }
        if ($type == 'history') {
            $api_url = 'https://' . $api_url . '/xml/recent_company_reviews.xml?connectorcode=' . $api_key . '&company_id=' . $api_id . '&reviewcount=10000';
        }


        if ($api_id) {
            $xml = simplexml_load_file($api_url);
            if ($xml) {
                return $xml;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Send invitation when order is completed
     * @param type $order
     * @return boolean
     */
    public function sendInvitation($order) {

        $store_id = $order->getStoreId();
        $start_time = microtime(true);
        $crontype = 'orderupdate';
        $order_id = $order->getIncrementId();
        $api_id = $this->getConfigValue('kiyoh/general/api_id', $store_id);
        $api_key = $this->getConfigValue('kiyoh/general/api_key', $store_id);
        $api_url = $this->getConfigValue('kiyoh/general/api_url', $store_id);
        $api_email = $this->getConfigValue('kiyoh/invitation/company_email', $store_id);
        $delay = $this->getConfigValue('kiyoh/invitation/delay', $store_id);
        $inv_status = $this->getConfigValue('kiyoh/invitation/status', $store_id);
        $email = strtolower($order->getCustomerEmail());

        if ($order->getStatus() == $inv_status) {
            $this->curl->setTimeout(30);

            $url = 'https://' . $api_url . '/set.php';
            $request = "action=sendInvitation&connector=" . $api_key . "&targetMail=" . $email . "&delay=" . $delay . "&user=" . $api_email;
            $this->curl->post($url, $request);
            $this->logger->debug('REQUEST: ' . $request);

            $result = $this->curl->getBody();
            $this->logger->debug('RESULT: ' . $result);

            if ($result) {
                $lines = explode("\n", $result);
                $response_html = $lines[0];
                $lines = array_reverse($lines);
                $response_html .= ' - ' . $lines[0];
            } else {
                $response_html = 'No response from ' . $url;
            }

            // Write to log
            $writelog = $this->kiyohLog->addToLog('invitation', $order->getStoreId(), '', $response_html, (microtime(true) - $start_time), $crontype, $url . '?' . $request, $order->getId());
            return true;
        }
    }

    /**
     * Return all stores which have company id set
     * @return type
     */
    public function getStoreIds() {
        $store_ids = array();
        $api_ids = array();
        $stores = $this->storeManager->getStores();
        foreach ($stores as $store) {
            if ($store->getIsActive()) {
                $api_id = $this->getConfigValue('kiyoh/general/api_id', $store->getId());
                if (!in_array($api_id, $api_ids)) {
                    $api_ids[] = $api_id;
                    $store_ids[] = $store->getId();
                }
            }
        }
        return $store_ids;
    }

}
