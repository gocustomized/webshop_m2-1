<?php

namespace CustomConcepts\GhostShipment\Observer;

use CustomConcepts\GhostShipment\Api\GhostShipmentCreatorInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class InvoicePaidAfter implements ObserverInterface
{
    /**
     * @var GhostShipmentCreatorInterface
     */
    private $shipmentCreator;

    protected $logger;

    /**
     * @param GhostShipmentCreatorInterface $shipmentCreator
     */
    public function __construct(GhostShipmentCreatorInterface $shipmentCreator, \CustomConcepts\GhostShipment\Logger\Logger $logger)
    {
        $this->shipmentCreator = $shipmentCreator;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        $invoice = $observer->getEvent()->getData('invoice');

        /** @var \Magento\Sales\Model\Order $order */
        $order = $invoice->getOrder();
        $this->logger->info('Start Creating ghostshipment on InvoicePaidAfter event');
        if ($order->getId()) { //dont process orders that are not yet saved from DB. OrderSavedAfter observer handles those.
            $this->shipmentCreator->create($order);
            $this->logger->info('Ghostshipment created on InvoicePaidAfter event for order #' . $order->getIncrementId());
        }
    }
}
