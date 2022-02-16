<?php
declare(strict_types=1);

namespace CustomConcepts\GhostShipment\Controller\Adminhtml\Shipment;

use Bluebirdday\TranssmartSmartConnect\Model\Shipping\Method\Parser as ShippingMethodParser;
use Bluebirdday\TranssmartSmartConnectExtension\Controller\Adminhtml\AbstractShipment;
use Bluebirdday\TranssmartSmartConnectExtension\Model\Shipment\Manager as ShipmentManager;
use CustomConcepts\GhostShipment\Api\GhostShipmentCreatorInterface;
use CustomConcepts\GhostShipment\Api\GhostShipmentExceptionHandlerInterface;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\Order\Shipment;
use Magento\Sales\Model\Order\ShipmentRepository;
use Magento\Sales\Model\ResourceModel\Order\Shipment as ShipmentResource;
use Psr\Log\LoggerInterface;

//
/**
 * Overrides of Bluebirdday\TranssmartSmartConnectExtension\Controller\Adminhtml\Shipment\Book:
 * 1. Replaced in execute() redirect path from 'sales/shipment/view' to 'sales/order'
 * 2. Added to execute() $this->ghostShipmentExceptionHandler->handle($e, $shipment) line
 * 3. Overridden processShipment() - origin has only one line - $this->shipmentManager->bookShipments($shipment)
 */
class Book extends AbstractShipment
{
    /**
     * @var ShipmentResource
     */
    private $shipmentResource;

    /**
     * @var GhostShipmentExceptionHandlerInterface
     */
    private $ghostShipmentExceptionHandler;

    /**
     * @param Context $context
     * @param ShipmentRepository $shipmentRepository
     * @param ShippingMethodParser $methodParser
     * @param ShipmentManager $shipmentManager
     * @param LoggerInterface $logger
     * @param ShipmentResource $shipmentResource
     * @param GhostShipmentExceptionHandlerInterface $ghostShipmentExceptionHandler
     */
    public function __construct(
        Context $context,
        ShipmentRepository $shipmentRepository,
        ShippingMethodParser $methodParser,
        ShipmentManager $shipmentManager,
        LoggerInterface $logger,
        ShipmentResource $shipmentResource,
        GhostShipmentExceptionHandlerInterface $ghostShipmentExceptionHandler
    ) {
        parent::__construct($context, $shipmentRepository, $methodParser, $shipmentManager, $logger);
        $this->shipmentResource = $shipmentResource;
        $this->ghostShipmentExceptionHandler = $ghostShipmentExceptionHandler;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        $shipmentId = $this->getRequest()->getParam('shipment_id');
        try {
            /** @var Shipment $shipment */
            $shipment = $this->shipmentRepository->get($shipmentId);
        } catch (NoSuchEntityException $exception) {
            $this->messageManager->addErrorMessage(__('This shipment no longer exists.'));
            return $resultRedirect->setPath('sales/order');
        }

        $order = $shipment->getOrder();
        if (!$this->isTranssmartOrder($order)) {
            $this->messageManager->addErrorMessage(__('Cannot process non Transsmart order.'));
            return $resultRedirect->setPath('sales/order');
        }

        if (!$this->validateShipment($shipment)) {
            $this->messageManager->addErrorMessage(__('Cannot process shipment.'));
            return $resultRedirect->setPath('sales/order');
        }

        try {
            $this->processShipment($shipment);
            $this->messageManager->addSuccessMessage(__('Shipment was successfully processed.'));
        } catch (LocalizedException $e) {
            $this->logger->error($e->getMessage());
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->ghostShipmentExceptionHandler->handle($e, $shipment);
            return $resultRedirect->setPath('sales/order');
        } catch (\Exception $e) {
            $this->logger->error(__('An unknown error occurred while processing shipment: ') . $e->getMessage());
            $this->messageManager->addErrorMessage(__('An unknown error occurred while processing shipment.'));
            $this->ghostShipmentExceptionHandler->handle($e, $shipment);
            return $resultRedirect->setPath('sales/order');
        }

        return $resultRedirect->setPath('sales/order');
    }

    /**
     * @inheritdoc
     */
    protected function validateShipment(Shipment $shipment)
    {
        return $this->shipmentManager->isBookable($shipment);
    }

    /**
     * @inheritdoc
     */
    protected function processShipment(Shipment $shipment)
    {
        $shipment->setData(GhostShipmentCreatorInterface::GHOST_SHIPMENT_STATUS_COLUMN, 1);
        $this->shipmentResource->saveAttribute($shipment, 'ghost_shipment');
        $this->shipmentManager->bookShipments($shipment);
    }
}
