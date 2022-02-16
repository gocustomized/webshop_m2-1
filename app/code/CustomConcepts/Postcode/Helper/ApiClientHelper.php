<?php
/**
 * CustomConcepts_Postcode extension
 * @category  CustomConcepts_Extensions
 * @package   CustomConcepts_Postcode
 * @copyright Copyright (c) 2017
 * @author GoCustomized <info@gocustomized.com>
 */
namespace CustomConcepts\Postcode\Helper;

use Magento\Framework\App\Config;
use Magento\Checkout\Model\Session;
use Magento\Quote\Model\QuoteFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Webapi\Rest\Response;
use Magento\Store\Model\ScopeInterface;
use Flekto\Postcode\Helper\ApiClientHelper as MainHelper;

class ApiClientHelper extends MainHelper
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var scopeConfig
     */
    protected $scopeConfig;

    /**
     * @var Session
     */
    protected $_checkoutSession;

     /**
     * @var QuoteFactory
     */
    protected $quoteFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Response
     */
    protected $response;

    protected $quoteData;
    
    /**
     * Constructor
     *
     * @param Config $config
     * @param Session $_checkoutSession
     * @param QuoteFactory $quoteFactory
     * @param StoreManagerInterface $storeManager
     * @param Response $response
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(
        Config $config,
        Session $_checkoutSession,
        QuoteFactory $quoteFactory,
        StoreManagerInterface $storeManager,
        Response $response
    ) {
        $this->config = $config;
        $this->_checkoutSession = $_checkoutSession;
        $this->quoteFactory = $quoteFactory;
        $this->storeManager = $storeManager;
        $this->response = $response;
    }
    
    /**
     * getStoreConfig function.
     *
     * @access private
     * @param mixed $path
     * @return string|null
     */
    public function getStoreConfig($path): ?string
    {
        $cartData = $this->_checkoutSession->getQuote();
        $this->quoteData = $this->quoteFactory->create()->load($cartData->getEntityId());
        return $this->config->getValue($path, ScopeInterface::SCOPE_STORE, $this->quoteData->getStoreId());
    }

    /**
     * getKey function.
     *
     * @access private
     * @return string
     */
    private function getKey(): string
    {
        return trim($this->getStoreConfig('postcodenl_api/general/api_key'));
    }
}
