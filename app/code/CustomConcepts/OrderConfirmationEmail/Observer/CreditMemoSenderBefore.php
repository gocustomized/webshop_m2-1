<?php
namespace CustomConcepts\OrderConfirmationEmail\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class CreditMemoSenderBefore implements ObserverInterface
{
    public function execute(Observer $observer)
    {
        $transportObj = $observer->getTransport();
        $transportObj['creditmemo']->setGrandTotal(number_format($transportObj['creditmemo']->getGrandTotal(), 2));
    }
}