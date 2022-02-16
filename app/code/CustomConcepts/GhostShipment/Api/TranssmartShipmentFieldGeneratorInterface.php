<?php
declare(strict_types=1);

namespace CustomConcepts\GhostShipment\Api;

use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\OrderInterface;

interface TranssmartShipmentFieldGeneratorInterface
{
    /**
     * @param OrderInterface $order
     * @throw LocalizedException
     * @return DataObject
     */
    public function generate(OrderInterface $order) : DataObject;
}
