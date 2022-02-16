<?php
namespace CustomConcepts\GhostShipment\Controller\Adminhtml\Ship;


use CustomConcepts\GhostShipment\Api\GhostShipmentCreatorInterface;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Sales\Api\Data\OrderInterface;

class ship extends \Magento\Backend\App\Action
{
    protected $orderResource;
    protected $orderFactory;
    protected $shipmentNotifier;
    protected $resultFactory;
    protected $shipmentRepository;
    protected $orderRepository;

    public function __construct(
        Context $context,
        \Magento\Sales\Model\Spi\OrderResourceInterface $orderResource,
        \Magento\Sales\Api\Data\OrderInterfaceFactory $orderFactory,
        \Magento\Shipping\Model\ShipmentNotifier $shipmentNotifier,
        \Magento\Framework\Controller\ResultFactory $resultFactory,
        \Magento\Sales\Api\ShipmentRepositoryInterface $shipmentRepository,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
    ){
        $this->orderResource = $orderResource;
        $this->orderFactory = $orderFactory;
        $this->shipmentNotifier = $shipmentNotifier;
        $this->resultFactory = $resultFactory;
        $this->shipmentRepository = $shipmentRepository;
        $this->orderRepository = $orderRepository;
        parent::__construct($context);
    }

    public function execute()
    {
        if($orderNumbers = $this->getRequest()->getParam('order_numbers')){
            $orderNumbers = explode(',', $orderNumbers);

            $ordersUpdated = 0;
            foreach ($orderNumbers as $orderNumber){
                /** @var \Magento\Sales\Model\Order $order */
                $order = $this->orderFactory->create();
                $this->orderResource->load($order, $orderNumber, OrderInterface::INCREMENT_ID);

                if($order->getId()){
                    if($order->canShip() && $order->hasInvoices()){
                        /** @var \Magento\Sales\Model\Order\Shipment $shipment */
                        foreach ($order->getShipmentsCollection() as $shipment){
                            try {
                                $shipment->register();
                                $shipment->setData(GhostShipmentCreatorInterface::GHOST_SHIPMENT_STATUS_COLUMN, 0);
                                $shipment->setCreatedAt(date('Y-m-d H:i:s'));
                                $this->shipmentNotifier->notify($shipment);
                                $this->shipmentRepository->save($shipment);
                                $this->orderRepository->save($shipment->getOrder());
                                $ordersUpdated += 1;
                            } catch (\Magento\Framework\Exception\LocalizedException $e){
                                $this->messageManager->addErrorMessage(__('order# ' . $order->getIncrementId() . ': ' . $e->getMessage()));
                            }
                        }
                    } else {
                        $this->messageManager->addErrorMessage(__('order# ' . $order->getIncrementId() . ': order is already shipped or doesnt have an invoice yet.'));
                    }
                } else {
                    $this->messageManager->addErrorMessage(__('order# ' . $orderNumber . ': doesnt exist.'));
                }
            }

            $this->messageManager->addSuccessMessage(__('A total of %1 orders has been shipped.', $ordersUpdated));
        }

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/index');
    }
}
