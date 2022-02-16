<?php
declare(strict_types=1);

namespace CustomConcepts\FulfillmentDashboardIntegration\Api;

interface DashboardApiAdapterInterface
{
    const SUCCESS_SHIPMENT_CREATION_RESPONSE_STATUS = 201;

    public function sendShipmentsCreationRequest(array $data) : bool;

    public function getResponse() : string;

    public function getStatusResponse() : int;
}
