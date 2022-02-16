<?php
declare(strict_types=1);

namespace CustomConcepts\Tableratezipranges\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Config extends AbstractHelper
{
    const XML_PATH_ENABLE_TABLE_ZIP_RANGES = 'cc_ghost_shipment/general/enabled';
    const XML_PATH_ENABLE_CORE_TABLE_RATES = 'carriers/tablerate/active';

    /**
     * @return bool
     */
    public function isTableRateZipRangesEnabled(): bool
    {
        return
            $this->scopeConfig->isSetFlag(self::XML_PATH_ENABLE_TABLE_ZIP_RANGES, ScopeInterface::SCOPE_WEBSITE)
            && $this->scopeConfig->isSetFlag(self::XML_PATH_ENABLE_CORE_TABLE_RATES, ScopeInterface::SCOPE_WEBSITE);
    }
}
