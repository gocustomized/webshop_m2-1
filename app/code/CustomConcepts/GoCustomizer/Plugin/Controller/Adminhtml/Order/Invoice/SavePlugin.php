<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace CustomConcepts\GoCustomizer\Plugin\Controller\Adminhtml\Order\Invoice;

class SavePlugin {

    /**
     * @var \Magento\Framework\Controller\Result\RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Sales\Model\Order\Item
     */
    protected $orderItem;

    /**
     * 
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
    \Magento\Backend\App\Action\Context $context, \Magento\Framework\Registry $registry, \Magento\Sales\Model\Order\Item $orderItem, \Magento\Sales\Model\Order $order
    ) {
        $this->registry = $registry;
        $this->orderItem = $orderItem;
        $this->resultRedirectFactory = $context->getResultRedirectFactory();
    }

    public function afterExecute(
    \Magento\Sales\Controller\Adminhtml\Order\Invoice\Save $subject
    ) {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $orderItems = $this->registry->registry('exporteditem');
        //echo '<pre>';print_r($orderItems);die;
        $orderId = null;
        if (isset($orderItems) && count($orderItems) > 0) {

            foreach ($orderItems as $orderData) {
                
                if (isset($orderData['items'])) {
                   
                    $_item = null;
                    foreach ($orderData['items'] as $itemId => $_itemData) {
                        $_item = $this->orderItem->load($itemId);
                        $_item->setExportData($_itemData['export_data']);
                        $_item->setExportedId($_itemData['exported_id']);
                        $_item->save();
                        $orderId = $_item->getOrderId();
                    }
                }
            }
        }
        $this->registry->unregister('exporteditem');
        return $resultRedirect->setPath('sales/order/view', ['order_id' => $orderId]);
    }

}
