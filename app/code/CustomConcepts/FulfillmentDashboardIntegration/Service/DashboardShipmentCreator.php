<?php
declare(strict_types=1);

namespace CustomConcepts\FulfillmentDashboardIntegration\Service;

use CustomConcepts\FulfillmentDashboardIntegration\Api\DashboardApiAdapterInterface;
use CustomConcepts\FulfillmentDashboardIntegration\Api\DashboardShipmentCreatorInterface;
use CustomConcepts\FulfillmentDashboardIntegration\Exception\DashboardShipmentCreationException;
use CustomConcepts\FulfillmentDashboardIntegration\Helper\Config;
use CustomConcepts\FulfillmentDashboardIntegration\Logger\Logger;

class DashboardShipmentCreator implements DashboardShipmentCreatorInterface
{
    /**
     * @var DashboardApiAdapterInterface
     */
    private $adapter;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param DashboardApiAdapterInterface $adapter
     * @param Logger $logger
     * @param Config $config
     */
    public function __construct(DashboardApiAdapterInterface $adapter, Logger $logger, Config $config)
    {
        $this->adapter = $adapter;
        $this->logger = $logger;
        $this->config = $config;
    }

    /**
     * @inheritDoc
     */
    public function create(array $data): bool
    {
        if (!$this->config->isDashboardIntegrationEnabled()) {
            $this->logger->notice('Dashboard integration is disabled, shipment creation skipped');

            return false;
        }
        $this->logger->notice('Start shipment creation');
        if (!$this->config->getDashboardDomain()) {
            throw new DashboardShipmentCreationException(__('Dashboard domain is empty! Please fill it on the configuration.'));
        }

        if ($this->adapter->sendShipmentsCreationRequest($data)) {
            $this->logger->notice($this->adapter->getResponse());
            return true;
        } else {
            $this->logger->error(
                "Connection with the Fulfillment Dashboard failed. The response status is {$this->adapter->getStatusResponse()}"
            );
            $this->logger->error($this->adapter->getResponse());
            throw new DashboardShipmentCreationException(
                __("Connection with the Fulfillment Dashboard failed. The response status is {$this->adapter->getStatusResponse()}.
                Please check logs for the details")
            );
        }
    }
}
