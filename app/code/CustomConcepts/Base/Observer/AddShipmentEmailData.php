<?php
namespace CustomConcepts\Base\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class AddShipmentEmailData implements ObserverInterface
{
    public function execute(Observer $observer)
    {
        $transport = $observer->getTransport();
        $transportObject = $observer->getData('transportObject');
        $order = $transport['order'];

        $transport['created_at_formatted'] = $order->getCreatedAtFormatted(2);
        $transportObject->setData('created_at_formatted', $order->getCreatedAtFormatted(2));
    }
}
