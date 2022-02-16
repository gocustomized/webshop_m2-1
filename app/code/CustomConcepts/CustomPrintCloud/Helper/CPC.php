<?php

namespace CustomConcepts\CustomPrintCloud\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface as ScopeInterfaceAlias;

/**
 * Class CPC
 *
 * @package CustomConcepts\CustomPrintCloud\Helper
 */
class CPC extends AbstractHelper
{
    protected $scopeConfig;
    const CONFIG_PATH = 'default/products/general/';

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        Context $context
    ) {
        $this->_scopeConfig = $scopeConfig;
        parent::__construct($context);
    }

    public function getCPCConfig($key)
    {
        return $this->scopeConfig->getValue(
            Self::CONFIG_PATH . trim($key),
            ScopeInterfaceAlias::SCOPE_STORE
        );
    }
}
