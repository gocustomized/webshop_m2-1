<?php
declare(strict_types=1);

namespace CustomConcepts\GhostShipment\Api;

use Magento\Sales\Api\Data\OrderInterface;

interface GhostShipmentCreatorInterface
{
    const GHOST_SHIPMENT_STATUS_COLUMN = 'ghost_shipment';

    /**
     * @param OrderInterface $order
     * @throws \Exception
     * @return bool
     */
    public function create(OrderInterface $order) : bool;
}
