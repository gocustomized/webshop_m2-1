<?php
declare(strict_types=1);

namespace CustomConcepts\GhostShipment\Model\Shipment;

use CustomConcepts\GhostShipment\Api\GhostShipmentCreatorInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;

class Synchronizer extends \Bluebirdday\TranssmartSmartConnectExtension\Model\Shipment\Synchronizer
{
    /**
     * @var \Magento\Shipping\Model\ShipmentNotifier
     */
    private $shipmentNotifier;

    /**
     * @var \Bluebirdday\TranssmartSmartConnect\Model\Transsmart\Adapter
     */
    private $adapter;

    protected $ghostShipmentlogger;

    public function __construct(
        \CustomConcepts\GhostShipment\Model\Transsmart\Adapter $adapter,
        \Bluebirdday\TranssmartSmartConnect\Model\Transsmart\SyncManagement $syncManagement,
        \Bluebirdday\TranssmartSmartConnect\Model\Transsmart\SyncFactory $syncFactory,
        \Bluebirdday\TranssmartSmartConnect\Model\Transsmart\SyncRepository $syncRepository,
        \Magento\Sales\Model\Order\ShipmentRepository $shipmentRepository,
        \Bluebirdday\TranssmartSmartConnect\Model\IncotermRepository $incotermRepository,
        \Bluebirdday\TranssmartSmartConnect\Model\Servicelevel\TimeRepository $timeRepository,
        \Bluebirdday\TranssmartSmartConnect\Model\Servicelevel\OtherRepository $otherRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Sales\Model\ResourceModel\Order\Shipment $shipmentResource,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Sales\Model\Order\AddressRepository $repositoryAddress,
        \Magento\Shipping\Model\ShipmentNotifier $shipmentNotifier,
        \CustomConcepts\GhostShipment\Logger\Logger $ghostShipmentlogger
    ) {
        parent::__construct(
            $adapter,
            $syncManagement,
            $syncFactory,
            $syncRepository,
            $shipmentRepository,
            $incotermRepository,
            $timeRepository,
            $otherRepository,
            $searchCriteriaBuilder,
            $shipmentResource,
            $scopeConfig,
            $logger,
            $repositoryAddress
        );

        $this->shipmentNotifier = $shipmentNotifier;
        $this->adapter = $adapter;
        $this->ghostShipmentlogger = $ghostShipmentlogger;
    }

    /**
     * @inheritDoc
     * @throws LocalizedException
     */
    protected function updateBasicData(DataObject $updates, \Magento\Sales\Model\Order\Shipment $shipment)
    {
        parent::updateBasicData($updates, $shipment);
        $this->ghostShipmentlogger->info('Start GhostShipment Update for Order #' . $shipment->getOrder()->getIncrementId());
        try {
            $shipmentStatus = $updates->getDataByPath(sprintf('%s/shipmentStatus', $shipment->getIncrementId()));
            $this->ghostShipmentlogger->info('Status: ' . $shipmentStatus);
            $this->ghostShipmentlogger->info('GhostShipment Status: ' . $shipment->getData(GhostShipmentCreatorInterface::GHOST_SHIPMENT_STATUS_COLUMN));
            if ($shipmentStatus === \Bluebirdday\TranssmartSmartConnect\Model\ShipmentStatuses::TRANSSMART_SHIPMENT_STATUS_LABL
            && $shipment->getOrder()->canShip()) {
                $shipment->register();
                $this->ghostShipmentlogger->info('Shipment registration successful.');
                $shipment->setData(GhostShipmentCreatorInterface::GHOST_SHIPMENT_STATUS_COLUMN, 0);
                $this->ghostShipmentlogger->info('GhostShipment column update successful.');
                $shipment->setCreatedAt(date('Y-m-d H:i:s'));
                $this->ghostShipmentlogger->info('CreatedAt Update successful.');
                $this->shipmentNotifier->notify($shipment);
                $this->ghostShipmentlogger->info('Shipment Notify successful.');
                // order status will set to complete automatically
            }
            $this->ghostShipmentlogger->info('Update Sucessful.');
        } catch (\Exception $e) {
            $this->ghostShipmentlogger->info('Error Occured. ' . $e->getMessage());
            throw new LocalizedException(
                __($e->getMessage())
            );
        }
        $this->ghostShipmentlogger->info('End GhostShipment Update for Order #' . $shipment->getOrder()->getIncrementId());
    }
}
