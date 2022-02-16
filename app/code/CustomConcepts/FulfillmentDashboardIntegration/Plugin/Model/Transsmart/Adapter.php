<?php
declare(strict_types=1);

namespace CustomConcepts\FulfillmentDashboardIntegration\Plugin\Model\Transsmart;

use Bluebirdday\TranssmartSmartConnect\Model\Transsmart\Adapter as TranssmartApiAdapter;
use CustomConcepts\FulfillmentDashboardIntegration\Api\DashboardShipmentCreatorInterface;
use Magento\Framework\Exception\LocalizedException;

class Adapter
{
    /**
     * @var DashboardShipmentCreatorInterface
     */
    private $dashboardShipmentCreator;

    private $logger;

    /**
     * @param DashboardShipmentCreatorInterface $dashboardShipmentCreator
     */
    public function __construct(
        DashboardShipmentCreatorInterface $dashboardShipmentCreator,
        \CustomConcepts\FulfillmentDashboardIntegration\Logger\Logger $logger
    ){
        $this->dashboardShipmentCreator = $dashboardShipmentCreator;
        $this->logger = $logger;
    }

    public function beforeBookShipments(TranssmartApiAdapter $subject, array $transsmartShipments)
    {
        try {
            $this->dashboardShipmentCreator->create($transsmartShipments);
        } catch (LocalizedException $e){
            $this->logger->error($e->getMessage());
        }
        
        return null;
    }
}
