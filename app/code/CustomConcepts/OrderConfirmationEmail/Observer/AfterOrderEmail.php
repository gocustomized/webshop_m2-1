<?php
namespace CustomConcepts\OrderConfirmationEmail\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order\Email\Sender\OrderSender;
use Psr\Log\LoggerInterface;

class AfterOrderEmail implements ObserverInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var OrderSender
     */
    private $orderSender;

    /**
     * AfterInvoiceEmail constructor.
     * @param LoggerInterface $logger
     * @param OrderSender $orderSender
     */
    public function __construct(
        LoggerInterface $logger,
        OrderSender $orderSender
    ) {
        $this->logger = $logger;
        $this->orderSender = $orderSender;
    }

    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getData('order');

        if ($order->hasInvoices()) {
            if ($order->getCanSendNewEmailFlag() && $order->getId()) {
                try {
                    $this->orderSender->send($order);
                } catch (\Exception $e) {
                    $this->logger->critical($e);
                }
            }
        }
    }
}
