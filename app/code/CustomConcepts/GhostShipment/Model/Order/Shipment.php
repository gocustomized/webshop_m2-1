<?php
namespace CustomConcepts\GhostShipment\Model\Order;

use CustomConcepts\GhostShipment\Api\GhostShipmentCreatorInterface;

class Shipment extends \Magento\Sales\Model\Order\Shipment
{
    /**
     * Registers shipment.
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function register()
    {
        if ($this->getId() && !$this->getData(GhostShipmentCreatorInterface::GHOST_SHIPMENT_STATUS_COLUMN)) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('We cannot register an existing shipment')
            );
        }

        $totalQty = 0;

        /** @var \Magento\Sales\Model\Order\Shipment\Item $item */
        foreach ($this->getAllItems() as $item) {
            if ($item->getQty() > 0) {
                $item->register();

                if (!$item->getOrderItem()->isDummy(true)) {
                    $totalQty += $item->getQty();
                }
            }
        }

        $this->setTotalQty($totalQty);

        return $this;
    }
}
