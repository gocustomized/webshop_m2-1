<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace CustomConcepts\Base\Controller\Adminhtml\Orderextrareference;

use Magento\Framework\Controller\ResultFactory;

class Index extends \Magento\Backend\App\Action {

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $salesOrderFactory;
    /**
     * 
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     */
    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Sales\Model\OrderFactory $salesOrderFactory, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Magento\Framework\App\ResourceConnection $resourceConnection
    ) {
        $this->salesOrderFactory = $salesOrderFactory;
        $this->scopeConfig = $scopeConfig;
        $this->resourceConnection = $resourceConnection;
        parent::__construct($context);
    }
    
    protected function _isAllowed() {
        return $this->_authorization->isAllowed('CustomConcepts_Base::config_gocustomized_orderextrarerefrence');
    }
    
    public function execute() {
        
        $params = $this->getRequest()->getParams();
        $order_id = $params['order_id'];
        $order_note = $params['order_note']; 
        
        $order = $this->salesOrderFactory->create()->load($order_id);
        $order->setData("order_note",$order_note);
        $order->save();
        
        $message = __('Order note has been saved successfully');
        $this->messageManager->addSuccess($message);
        
    }

}
