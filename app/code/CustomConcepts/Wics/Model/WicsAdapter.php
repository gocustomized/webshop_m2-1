<?php

namespace CustomConcepts\Wics\Model;

use CustomConcepts\Wics\Api\Data\WicsItemResponseInterface;
use CustomConcepts\Wics\Api\Data\WicsItemResponseInterfaceFactory;
use Magento\Config\Model\Config\Backend\Encrypted;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Store\Model\ScopeInterface;

class WicsAdapter
{
    const XML_PATH_TO_API_CONFIG = 'cc_wics/api';

    /**
     * @var Curl
     */
    private $curl;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var Encrypted
     */
    private $encrypted;

    /**
     * @var string
     */
    private $url = '';

    /**
     * @var WicsItemResponseInterfaceFactory
     */
    private $collectionFactory;

    /**
     * @param Curl $curl
     * @param ScopeConfigInterface $scopeConfig
     * @param Encrypted $encrypted
     * @param WicsItemResponseInterfaceFactory $collectionFactory
     */
    public function __construct(
        Curl $curl,
        ScopeConfigInterface $scopeConfig,
        Encrypted $encrypted,
        WicsItemResponseInterfaceFactory $collectionFactory
    ) {
        $this->curl = $curl;
        $this->scopeConfig = $scopeConfig;
        $this->encrypted = $encrypted;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @param $path
     * @param bool $required
     * @return mixed
     */
    private function getConfigValue($path, $required = true)
    {
        if ($required && !$this->scopeConfig->isSetFlag($path)) {
            throw new \RuntimeException("Config path '$path' not found!");
        }

        return $this->scopeConfig->getValue(
            $path,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Read configuration and init curl
     */
    private function initConfig()
    {
        if ($this->url) {
            //we imply config is already initialized
            return;
        }
        $this->url = $this->getConfigValue(self::XML_PATH_TO_API_CONFIG . "/base_url");
        $username = $this->getConfigValue(self::XML_PATH_TO_API_CONFIG . "/key");
        $encryptedPassword = $this->getConfigValue(self::XML_PATH_TO_API_CONFIG . "/secret");
        $password = $this->encrypted->processValue($encryptedPassword);
        $this->curl->setCredentials($username, $password);
    }

    /**
     * @return WicsItemResponseInterface
     */
    public function getWicsItems() : WicsItemResponseInterface
    {
        $this->initConfig();
        $stockData = [];
        $pager = 1;
        while ($pager) {
            $this->curl->get($this->url . '/api/stock/list?page=' . $pager);
            $response = $this->curl->getBody();
            if ($response) {
                $stock = json_decode($response, true);
                if ($stock['data']) {
                    $stockData = array_merge($stockData, $stock['data']);
                    $pager++;
                } else {
                    $pager = null;
                }
            } else {
                $pager = null;
            }
        }

        return $this->wrapResultToCollection($stockData);
    }

    /**
     * @param array $stockData
     * @return WicsItemResponseInterface
     */
    private function wrapResultToCollection(array $stockData) : WicsItemResponseInterface
    {
        $collection = $this->collectionFactory->create();
        foreach ($stockData as $item) {
            $collection->addItem($item);
        }

        return $collection;
    }
}
