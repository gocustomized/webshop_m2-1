<?php
namespace CustomConcepts\GoCustomizer\Observer;


use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class OrderToQuote implements ObserverInterface
{
    protected $objectCopyService;

    public function __construct(
        \Magento\Framework\DataObject\Copy $objectCopyService
    ){
        $this->objectCopyService = $objectCopyService;
    }

    public function execute(Observer $observer)
    {
        $orderItem = $observer->getOrderItem();
        $quoteItem = $observer->getQuoteItem();

        $this->objectCopyService->copyFieldsetToTarget('sales_convert_order_item', 'to_quote_item', $orderItem, $quoteItem);

        return $this;
    }

}
