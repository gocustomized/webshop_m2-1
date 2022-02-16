<?php
declare(strict_types=1);

namespace CustomConcepts\GhostShipment\Api;

use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Framework\Exception\LocalizedException;

interface GhostShipmentExceptionHandlerInterface
{
    /**
     * @param \Exception $exception
     * @param ShipmentInterface $shipment
     * @throw LocalizedException
     */
    public function handle(\Exception $exception, ShipmentInterface $shipment) : void;
}
