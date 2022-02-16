<?php
declare(strict_types=1);

namespace CustomConcepts\GhostShipment\Observer;

use Bluebirdday\TranssmartSmartConnectExtension\Model\Shipment\Manager;
use CustomConcepts\GhostShipment\Api\GhostShipmentExceptionHandlerInterface;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;

class BookShipmentObserver implements ObserverInterface
{
    /**
     * @var Manager
     */
    private $shipmentManager;

    /**
     * @var ManagerInterface
     */
    private $messageManager;

    /**
     * @var GhostShipmentExceptionHandlerInterface
     */
    private $ghostShipmentExceptionHandler;

    protected $logger;

    /**
     * @param Manager $shipmentManager
     * @param ManagerInterface $messageManager
     * @param GhostShipmentExceptionHandlerInterface $ghostShipmentExceptionHandler
     */
    public function __construct(
        Manager $shipmentManager,
        ManagerInterface $messageManager,
        GhostShipmentExceptionHandlerInterface $ghostShipmentExceptionHandler,
        \CustomConcepts\GhostShipment\Logger\Logger $logger
    ) {
        $this->shipmentManager = $shipmentManager;
        $this->messageManager = $messageManager;
        $this->ghostShipmentExceptionHandler = $ghostShipmentExceptionHandler;
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     * @throws \Exception
     */
    public function execute(EventObserver $observer)
    {
        /** @var \Magento\Sales\Model\Order\Shipment $shipment */
        $shipment = $observer->getEvent()->getData('shipment');

        $bookFlag = (bool) $shipment->getData('transsmart_book');
        $printFlag = (bool) $shipment->getData('transsmart_bookandprint');

        if (!$bookFlag && !$printFlag) {
            return;
        }

        try {
            $this->shipmentManager->bookShipments($shipment, $printFlag);
        } catch (LocalizedException $exception) {
//            $this->messageManager->addErrorMessage($exception->getMessage());
            $this->logger->error($exception->getMessage());
            $this->ghostShipmentExceptionHandler->handle($exception, $shipment);
        } catch (\Zend_Json_Exception $exception) {
//            $this->messageManager->addErrorMessage($exception->getMessage());
            $this->logger->error($exception->getMessage());
            $this->ghostShipmentExceptionHandler->handle($exception, $shipment);
            throw $exception;
        }
    }
}
