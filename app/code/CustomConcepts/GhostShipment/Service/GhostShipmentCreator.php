<?php
declare(strict_types=1);

namespace CustomConcepts\GhostShipment\Service;

use CustomConcepts\GhostShipment\Api\GhostShipmentCreatorInterface;
use CustomConcepts\GhostShipment\Api\GhostShipmentExceptionHandlerInterface;
use CustomConcepts\GhostShipment\Api\TranssmartShipmentFieldGeneratorInterface;
use CustomConcepts\GhostShipment\Helper\Config;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Sales\Api\ShipmentRepositoryInterface;
use Magento\Sales\Model\Convert\Order as OrderConverter;
use Magento\Sales\Model\Order\Shipment;

class GhostShipmentCreator implements GhostShipmentCreatorInterface
{
    /**
     * @var TranssmartShipmentFieldGeneratorInterface
     */
    private $transsmartFieldGenerator;

    /**
     * @var OrderConverter
     */
    private $orderConverter;

    /**
     * @var Config
     */
    private $ghostShipmentConfig;

    /**
     * @var ShipmentRepositoryInterface
     */
    private $shipmentRepo;

    /**
     * @var GhostShipmentExceptionHandlerInterface
     */
    private $ghostShipmentExceptionHandler;

    /**
     * @param TranssmartShipmentFieldGeneratorInterface $transsmartFieldGenerator
     * @param OrderConverter $orderConverter
     * @param Config $ghostShipmentConfig
     * @param ShipmentRepositoryInterface $shipmentRepo
     * @param GhostShipmentExceptionHandlerInterface $ghostShipmentExceptionHandler
     */
    public function __construct(
        TranssmartShipmentFieldGeneratorInterface $transsmartFieldGenerator,
        OrderConverter $orderConverter,
        Config $ghostShipmentConfig,
        ShipmentRepositoryInterface $shipmentRepo,
        GhostShipmentExceptionHandlerInterface $ghostShipmentExceptionHandler
    ) {
        $this->transsmartFieldGenerator = $transsmartFieldGenerator;
        $this->orderConverter = $orderConverter;
        $this->ghostShipmentConfig = $ghostShipmentConfig;
        $this->shipmentRepo = $shipmentRepo;
        $this->ghostShipmentExceptionHandler = $ghostShipmentExceptionHandler;
    }

    /**
     * @inheritDoc
     */
    public function create(OrderInterface $order): bool
    {
        if (!$this->ghostShipmentConfig->isGhostShipmentCreationEnabled()
            || !$order->canShip()
            || $this->hasGhostShipment($order)) {
            return false;
        }

        $shipment = $this->createGhostShipmentByOrder($order);

        try {
            $shipment->addData($this->transsmartFieldGenerator->generate($order)->getData());
            $this->shipmentRepo->save($shipment);
        } catch (\Exception $e) {
            $this->ghostShipmentExceptionHandler->handle($e, $shipment);
        }

        return true;
    }

    /**
     * @param OrderInterface $order
     * @return Shipment
     * @throws LocalizedException
     */
    private function createGhostShipmentByOrder(OrderInterface $order) : Shipment
    {
        $shipment = $this->orderConverter->toShipment($order);

        foreach ($order->getAllItems() as $orderItem) {
            if (!$orderItem->getQtyToShip() || $orderItem->getIsVirtual()) {
                continue;
            }

            $qtyShipped = $orderItem->getQtyToShip();
            $shipmentItem = $this->orderConverter->itemToShipmentItem($orderItem)->setQty($qtyShipped);
            $shipment->addItem($shipmentItem);
        }

//        $shipment->register();
        $order->setIsInProcess(true);
        $shipment->setData(self::GHOST_SHIPMENT_STATUS_COLUMN, 1);
        $shipment->addComment("Automatically created shipment", false, false);

        return $shipment;
    }

    /**
     * @param OrderInterface $order
     * @return bool
     */
    private function hasGhostShipment(OrderInterface $order): bool
    {
        /** @var $order \Magento\Sales\Model\Order */
        $shipments = $order->getShipmentsCollection();
        if($shipments){
            foreach ($shipments as $shipment){
                if($shipment->getData(self::GHOST_SHIPMENT_STATUS_COLUMN)){
                    return true;
                }
            }
        }

        return false;
    }
}
