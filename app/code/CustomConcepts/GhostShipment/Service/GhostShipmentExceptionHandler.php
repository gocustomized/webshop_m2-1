<?php
declare(strict_types=1);

namespace CustomConcepts\GhostShipment\Service;

use CustomConcepts\GhostShipment\Api\GhostShipmentCreatorInterface;
use CustomConcepts\GhostShipment\Api\GhostShipmentExceptionHandlerInterface;
use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Sales\Model\ResourceModel\Grid;
use Magento\Sales\Model\ResourceModel\Order\Shipment as ShipmentResource;

class GhostShipmentExceptionHandler implements GhostShipmentExceptionHandlerInterface
{
    /**
     * @var ShipmentResource
     */
    private $shipmentResource;

    /**
     * @var Grid
     */
    private $shipmentGrid;

    /**
     * @param ShipmentResource $shipmentResource
     * @param Grid $shipmentGrid
     */
    public function __construct(ShipmentResource $shipmentResource, Grid $shipmentGrid)
    {
        $this->shipmentResource = $shipmentResource;
        $this->shipmentGrid = $shipmentGrid;
    }

    /**
     * @inheritDoc
     */
    public function handle(\Exception $exception, ShipmentInterface $shipment): void
    {
        // return status from "ghost" to "new"
        $shipment->setData(GhostShipmentCreatorInterface::GHOST_SHIPMENT_STATUS_COLUMN, 0);
        $attributesToUpdate = ['ghost_shipment'];
        if ($shipment->dataHasChangedFor('transsmart_status')) {
            $attributesToUpdate[] = 'transsmart_status';
        }
        if ($shipment->dataHasChangedFor('transsmart_shipment_error')) {
            $attributesToUpdate[] = 'transsmart_shipment_error';
        }
        $this->shipmentResource->saveAttribute($shipment, $attributesToUpdate);
        // add shipment to the grid
        $this->shipmentGrid->refresh($shipment->getId());
    }
}
