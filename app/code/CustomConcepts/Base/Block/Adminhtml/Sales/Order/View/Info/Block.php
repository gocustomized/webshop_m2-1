<?php

namespace CustomConcepts\Base\Block\Adminhtml\Sales\Order\View\Info;

class Block extends \Magento\Framework\View\Element\Template {

    const XML_PATH_ORDER_NOTE = 'orderextrarerefrence/general/order_note';
    
    protected $order;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\Dat
     * @var typeaObjectFactory
     */
    protected $dataObjectFactory;
    
    /**
     * @var $context->getScopeConfig();
     */
    protected $scopeConfig;

    public function __construct(
    \Magento\Framework\View\Element\Template\Context $context, \Magento\Framework\Registry $registry, \Magento\Framework\DataObjectFactory $dataObjectFactory, array $data = []
    ) {
        $this->dataObjectFactory = $dataObjectFactory;
        $this->scopeConfig = $context->getScopeConfig();
        $this->registry = $registry;
        parent::__construct(
                $context, $data
        );
    }

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
    
    public function getConfigOrderNote(){
        $string = $this->scopeConfig->getValue(self::XML_PATH_ORDER_NOTE,\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $order_note = explode(',', $string);
        $order_note = array_map('trim',$order_note);
        return $order_note;
    }
    

}
