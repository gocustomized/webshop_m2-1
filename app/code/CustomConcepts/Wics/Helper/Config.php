<?php
declare(strict_types=1);

namespace CustomConcepts\Wics\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Config extends AbstractHelper
{
    const XML_PATH_ENABLE_WICS_INTEGRATION = 'cc_wics/general/enabled';

    /**
     * @return bool
     */
    public function isWicsIntegrationEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLE_WICS_INTEGRATION,
            ScopeInterface::SCOPE_STORE
        );
    }
}
