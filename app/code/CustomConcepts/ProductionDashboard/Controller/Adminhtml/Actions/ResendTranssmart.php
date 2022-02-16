<?php
namespace CustomConcepts\ProductionDashboard\Controller\Adminhtml\Actions;


use Bluebirdday\TranssmartSmartConnect\Model\ShipmentStatuses;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;

class ResendTranssmart extends \Magento\Framework\App\Action\Action
{
    protected $shipmentCreator;
    protected $jsonFactory;
    protected $orderItemRepository;
    protected $logger;
    protected $shipmentManager;

    public function __construct(
        Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \CustomConcepts\GhostShipment\Api\GhostShipmentCreatorInterface $shipmentCreator,
        \Magento\Sales\Api\OrderItemRepositoryInterface $orderItemRepository,
        \Psr\Log\LoggerInterface $logger,
        \Bluebirdday\TranssmartSmartConnectExtension\Model\Shipment\Manager $shipmentManager
    ){
        $this->jsonFactory = $jsonFactory;
        $this->shipmentCreator = $shipmentCreator;
        $this->orderItemRepository = $orderItemRepository;
        $this->logger = $logger;
        $this->shipmentManager = $shipmentManager;
        parent::__construct($context);
    }

    public function execute()
    {
        $result = $this->jsonFactory->create();

        try {
            $itemId = $this->getRequest()->getParam('item_id');

            /** @var $orderItem \Magento\Sales\Model\Order\Item */
            $orderItem = $this->orderItemRepository->get($itemId);
            /** @var $order \Magento\Sales\Model\Order */
            $order = $orderItem->getOrder();

            if($order->getInvoiceCollection()->count() && $order->getShipmentsCollection()->count()){
                try {
                    foreach($order->getShipmentsCollection() as $shipment){
                        $shipment->setData('transsmart_status', ShipmentStatuses::TRANSSMART_SHIPMENT_STATUS_ERR);
                        $this->shipmentManager->bookShipments($shipment);
                    }
                    $result->setData(['message' => 'Successfully sent to transsmart']);
                } catch (LocalizedException $exception) {
                    $this->logger->error($exception->getMessage());
                    $result->setData(['message' => $exception->getMessage()]);
                } catch (\Zend_Json_Exception $exception) {
                    $this->logger->error($exception->getMessage());
                    $result->setData(['message' => $exception->getMessage()]);
                }
            } else {
                $result->setHttpResponseCode(400);
                $result->setData(['errorMessage' => __('Item doesnt have an invoice yet.')]);
            }
        } catch (LocalizedException $exception) {
            $this->logger->alert($exception->getMessage());
            $result->setHttpResponseCode(400);
            $result->setData(['errorMessage' => $exception->getMessage()]);
        } catch (\RuntimeException $exception) {
            $this->logger->alert($exception->getMessage());
            $result->setHttpResponseCode(400);
            $result->setData(['errorMessage' => __('Cannot perform request. See log for details.')]);
        }

        return $result;
    }
}
