<?php

/**
 * CustomConcepts_ReorderDiscountCode extension
 * @category  CustomConcepts_Extensions
 * @package   CustomConcepts_ReorderDiscountCode
 * @copyright Copyright (c) 2017
 * @author GoCustomized <info@gocustomized.com>
 */

namespace CustomConcepts\ReorderDiscountCode\Block\Adminhtml\Sales\Order\View\Info;

class Block extends \Magento\Framework\View\Element\Template {
    /**
     *
     * @var \Magento\Sales\Model\Order 
     */
    protected $order;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\DataObjectFactory
     */
    protected $dataObjectFactory;

    /**
     *
     * @var type \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     *
     * @var type \Magento\Email\Model\Template
     */
    protected $template;

    /**
     * 
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\DataObjectFactory $dataObjectFactory
     * @param \Magento\Email\Model\Template $template
     * @param array $data
     */
    public function __construct(
    \Magento\Framework\View\Element\Template\Context $context, \Magento\Framework\Registry $registry, \Magento\Framework\DataObjectFactory $dataObjectFactory, \Magento\Email\Model\Template $template, array $data = []
    ) {
        $this->dataObjectFactory = $dataObjectFactory;
        $this->registry = $registry;
        $this->scopeConfig = $context->getScopeConfig();
        $this->template = $template;
        parent::__construct(
                $context, $data
        );
    }

    /**
     * Get config value
     * @param type $path
     * @param type $scopeType
     * @return mixed
     */
    public function getConfigValue($path, $scopeType = 0) {
        return $this->scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $scopeType);
    }

    /**
     * Get current order from registery
     * @return type
     */
    public function getOrder() {
        if (is_null($this->order)) {
            if ($this->registry->registry('current_order')) {
                $order = $this->registry->registry('current_order');
            } elseif ($this->registry->registry('order')) {
                $order = $this->registry->registry('order');
            } else {
                $order = $this->dataObjectFactory->create();
            }
            $this->order = $order;
        }
        return $this->order;
    }

    /**
     * Get email templates which is selected for stores
     * @return type
     */
    public function getTemplateCollection() {
        $storeId = $this->getOrder()->getStoreId();
        $templateIds = $this->getConfigValue('reorderdiscountcode/reorderdis_email/email_template', $storeId);
        $templateIds_array = explode(',', $templateIds);

        $template_collection = $this->template->getCollection()->addFieldToFilter('template_id', $templateIds_array);
        return $template_collection;
    }

}
