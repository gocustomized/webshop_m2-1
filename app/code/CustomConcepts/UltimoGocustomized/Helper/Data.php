<?php

namespace CustomConcepts\UltimoGocustomized\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper {

    const XML_PATH_ROOT_CATEGORY = "find_device/general/category_id";
    const XML_PATH_BESTSELLER_PRODUCTS = "bestseller/general/product_ids";
    
    protected $_scopeConfig;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->_scopeConfig = $scopeConfig;
        parent::__construct($context);
    }

    public function getConfig($config_path) {
        return $this->scopeConfig->getValue($config_path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

}
