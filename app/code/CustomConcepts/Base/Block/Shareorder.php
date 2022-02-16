<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace CustomConcepts\Base\Block;

class Shareorder extends \Magento\Framework\View\Element\Template {
    /*
     * \Magento\Sales\Model\Order
     */

    protected $order;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * \Magento\Sales\Model\Order
     */
    protected $orderObject;

    /**
     * @var \Magento\Eav\Api\AttributeSetRepositoryInterface 
     */
    protected $attributeSet;

    /**
     * @var \CustomConcepts\GoCustomizer\Helper\Data
     */
    protected $customizerHelper;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $helperImage;

    /**
     * 
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Sales\Model\Order $order
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Eav\Api\AttributeSetRepositoryInterface $attributeSet
     * @param \CustomConcepts\GoCustomizer\Helper\Data $customizerHelper
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Catalog\Helper\Image $helperImage
     * @param array $data
     */
    public function __construct(
    \Magento\Framework\View\Element\Template\Context $context, \Magento\Sales\Model\Order $order, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Magento\Eav\Api\AttributeSetRepositoryInterface $attributeSet, \CustomConcepts\GoCustomizer\Helper\Data $customizerHelper, \Magento\Checkout\Model\Session $checkoutSession, \Magento\Catalog\Helper\Image $helperImage, array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->order = $order;
        $this->scopeConfig = $scopeConfig;
        $this->customizerHelper = $customizerHelper;
        $this->attributeSet = $attributeSet;
        $this->_checkoutSession = $checkoutSession;
        $this->helperImage = $helperImage;
        $this->storeManager = $context->getStoreManager();
    }

    /**
     * Get an Order Object
     * @return Order object 
     */
    public function getOrder() {
        return $this->orderObject = $this->order->load($this->_checkoutSession->getLastOrderId());
    }

    /**
     * Get an Order Items
     * @return Order Items 
     */
    public function getItems() {
        return $this->orderObject->getAllItems();
    }

    /**
     * 
     * @return image helper object
     */
    public function getImageHelper() {
        return $this->helperImage;
    }

    /**
     * 
     * @param type $path
     * @return string|mix
     */
    public function getConfigValue($path) {
        return $this->scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->storeManager->getStore());
    }

    /**
     * 
     * @param type $path
     * @return string|mix
     */
    public function isActiveShareOrder() {
        return $this->scopeConfig->getValue('facebookShareOrder/general/shareorder_enabled', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->storeManager->getStore());
    }

    public function getAttributeSetName($attributeId) {
        return $this->attributeSet->get($attributeId)->getAttributeSetName();
    }

    public function getGocustomizerHelper() {
        return $this->customizerHelper;
    }

}
