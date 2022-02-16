<?php

/**
 * CustomConcepts_TranssmartShipping extension
 * @category  CustomConcepts_Extensions
 * @package   CustomConcepts_TranssmartShipping
 * @copyright Copyright (c) 2017
 * @author GoCustomized <info@gocustomized.com>
 */
namespace CustomConcepts\Base\Observer;
use Magento\Framework\Event\ObserverInterface;
class SalesOrderPlaceAfter  implements ObserverInterface {
    
       /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * 
     * @param \Magento\Framework\App\Request\Http $request
     */
    public function __construct(
        \Magento\Framework\App\Request\Http $request
    ) {
        $this->request = $request;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {
        $post_data = $this->request->getPost();
    	$order = $observer->getEvent()->getOrder();
        $order->setData('order_note',$this->request->getPost('order_note'));
    }
    
}