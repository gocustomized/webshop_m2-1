<?php
declare(strict_types=1);

namespace CustomConcepts\GhostShipment\Model\Transsmart;

use Magento\Framework\Exception\LocalizedException;

class Adapter extends \Bluebirdday\TranssmartSmartConnect\Model\Transsmart\Adapter
{
    /**
     * Request single shipment retrieval
     *
     * @see https://devdocs.transsmart.com/#_single_shipment_retrieval
     * @param string $shipmentId
     * @return mixed
     * @throws LocalizedException
     */
    public function retrieveShipmentDocument($shipmentId)
    {
        $this->initConfig();
        $url = "/v2/shipments/{$this->accountId}/$shipmentId";

        return json_decode($this->curlExec($url), true);
    }
}
