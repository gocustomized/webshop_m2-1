<?php
declare(strict_types=1);

namespace CustomConcepts\FulfillmentDashboardIntegration\Api;

use CustomConcepts\FulfillmentDashboardIntegration\Exception\DashboardShipmentCreationException;

interface DashboardShipmentCreatorInterface
{
    /**
     * @param array $data
     * @throws DashboardShipmentCreationException
     * @return bool
     */
    public function create(array $data) : bool;
}
