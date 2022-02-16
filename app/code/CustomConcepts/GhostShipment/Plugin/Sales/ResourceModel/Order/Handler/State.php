<?php
declare(strict_types=1);

namespace CustomConcepts\GhostShipment\Plugin\Sales\ResourceModel\Order\Handler;

use Bluebirdday\TranssmartSmartConnect\Model\OrderStatuses;
use Bluebirdday\TranssmartSmartConnect\Model\Shipping\Method\Parser;
use CustomConcepts\GhostShipment\Api\GhostShipmentCreatorInterface;
use Magento\Sales\Model\Order;

class State
{
    /**
     * @var Parser
     */
    protected $methodParser;

    /**
     * @param Parser $methodParser
     */
    public function __construct(Parser $methodParser)
    {
        $this->methodParser = $methodParser;
    }

    public function afterCheck(
        \Magento\Sales\Model\ResourceModel\Order\Handler\State $subject,
        $result,
        Order $order
    ) {
        $id = $order->getIncrementId();
        if ($this->isTranssmartOrder($order)
            && $shipments = $order->getShipmentsCollection()) {
            if ($order->getTranssmartStatus() == OrderStatuses::TRANSSMART_ORDER_STATUS_ERROR) {
                $this->setOrderProcessingStatus($order);

                return $result;
            }
//            /** @var \Magento\Sales\Model\Order\Shipment $shipment */
//            foreach ($shipments as $shipment) {
//                if ($shipment->getData(GhostShipmentCreatorInterface::GHOST_SHIPMENT_STATUS_COLUMN)) {
//                    $this->setOrderProcessingStatus($order);
//
//                    return $result;
//                }
//            }
        }

        return $result;
    }

    private function setOrderProcessingStatus(Order $order)
    {
        $order->setState(Order::STATE_PROCESSING)
            ->setStatus(Order::STATE_PROCESSING);
    }
    /**
     * Check if transsmart order.
     *
     * @param Order $order
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function isTranssmartOrder(Order $order)
    {
        return $this->methodParser->parseOrder($order) !== false;
    }
}
