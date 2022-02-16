<?php
declare(strict_types=1);

namespace CustomConcepts\FulfillmentDashboardIntegration\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Config extends AbstractHelper
{
    const XML_PATH_ENABLE_DASHBOARD_INTEGRATION = 'cc_dashboard_integration/general/enabled';
    const XML_PATH_DASHBOARD_DOMAIN = 'cc_dashboard_integration/api/domain';
    const XML_PATH_DASHBOARD_SHIPMENT_CREATION = 'cc_dashboard_integration/api/shipment_creation_path';
    const XML_PATH_DASHBOARD_SUPPLIER_NAME = 'cc_dashboard_integration/fields/supplier_name';

    /**
     * @return bool
     */
    public function isDashboardIntegrationEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLE_DASHBOARD_INTEGRATION,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return string
     */
    public function getDashboardDomain(): string
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_DASHBOARD_DOMAIN
        );
    }

    /**
     * @return string
     */
    public function getShipmentCreationPath(): string
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_DASHBOARD_SHIPMENT_CREATION
        );
    }

    /**
     * @return string
     */
    public function getSupplierName(): string
    {
        // @todo remove it after testing, if this option won`t use
        return $this->scopeConfig->getValue(
            self::XML_PATH_DASHBOARD_SUPPLIER_NAME
        );
    }
}
