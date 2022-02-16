<?php
declare(strict_types=1);

namespace CustomConcepts\FulfillmentDashboardIntegration\Plugin\Model\Shipment;

use Bluebirdday\TranssmartSmartConnect\Model\Data\Shipment as TranssmartShipmentData;
use Bluebirdday\TranssmartSmartConnectExtension\Model\Shipment\Creator as Subject;
use CustomConcepts\Estimations\Api\EstimationDatesRepositoryInterface;
use CustomConcepts\FulfillmentDashboardIntegration\Helper\Config;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Sales\Model\Order\Shipment;

class Creator
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var EstimationDatesRepositoryInterface
     */
    private $estimationRepository;

    /**
     * @param Config $config
     * @param ProductRepositoryInterface $productRepository
     * @param EstimationDatesRepositoryInterface $estimationRepository
     */
    public function __construct(
        Config $config,
        ProductRepositoryInterface $productRepository,
        EstimationDatesRepositoryInterface $estimationRepository
    ) {
        $this->config = $config;
        $this->productRepository = $productRepository;
        $this->estimationRepository = $estimationRepository;
    }

    public function afterInitDeliveryNoteInfo(
        Subject $subject,
        $result,
        TranssmartShipmentData $shipmentData,
        Shipment $shipment
    ) {
        if (!$this->config->isDashboardIntegrationEnabled()) {
            return;
        }
        if (!$deliveryNoteInfoLines = $shipmentData->getData('deliveryNoteInformation/deliveryNoteLines')) {
            return;
        }

        /** @var \Magento\Sales\Model\Order\Shipment\Item $item */
        foreach ($shipment->getAllItems() as $item) {
            if ($item->getOrderItem()->getParentItem()) {
                continue;
            }
            $sku = substr($item->getSku(), 0, 64);
            $key = array_search($sku, array_column($deliveryNoteInfoLines, 'articleId'));

            if ($key !== false) {
                $deliveryNoteInfoLines[$key]['sku'] = $sku;
                // override articleId by order_item_id
                $deliveryNoteInfoLines[$key]['articleId'] = $item->getOrderItemId();

                // set additional fields that the FD requires
                $product = $this->productRepository->get($sku);


                $deliveryNoteInfoLines[$key]['description'] = substr($product->getName(), 0, 128);

                if ($itemSizeAttr = $product->getCustomAttribute('item_size')) {
                    $deliveryNoteInfoLines[$key]['ItemSize'] = $itemSizeAttr->getValue();
                }else{
                    $deliveryNoteInfoLines[$key]['ItemSize'] = 0;
                }

                $deliveryNoteInfoLines[$key]['SupplierName'] = $product->getAttributeText('supplier');

                if ($estimation = $this->estimationRepository->getLatest($shipment->getOrderId())) {
                    $deliveryNoteInfoLines[$key]['ExpectedShippingDate'] = $estimation->getShippingDate();
                }
            }
        }
        $shipmentData->setData('deliveryNoteInformation', [
            'currency' => $shipment->getOrder()->getOrderCurrencyCode(),
            'deliveryNoteLines' => $deliveryNoteInfoLines
        ]);
    }
}
