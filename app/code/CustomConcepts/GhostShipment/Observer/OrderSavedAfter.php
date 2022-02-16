<?php

namespace CustomConcepts\GhostShipment\Observer;

use CustomConcepts\GhostShipment\Api\GhostShipmentCreatorInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class OrderSavedAfter implements ObserverInterface
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
        $order = $observer->getEvent()->getData('order');
        $this->logger->info('Start Creating ghostshipment on OrderSavedAfter event for order #' . $order->getIncrementId());
        if ($order->hasInvoices()) { // for orders that are created with invoices
            $this->shipmentCreator->create($order);
            $this->logger->info('Ghostshipment created on OrderSavedAfter event for order #' . $order->getIncrementId());
        }
    }
}
