<?php
declare(strict_types=1);

namespace CustomConcepts\TranssmartSmartConnect\Block\Adminhtml\Order\Create\Shipping\Method;

class Form extends \Magento\Sales\Block\Adminhtml\Order\Create\Shipping\Method\Form
{
    private function getGoogleMapsApiKey()
    {
        return $this->_scopeConfig->getValue(
            'transsmart/pickup/googlemaps_api_key',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getSearchUrl() : string
    {
        return $this->getUrl('cc_transsmart/pickup/location');
    }

    public function getGoogleMapApiScript() : string
    {
        if ($this->getGoogleMapsApiKey()) {
            return '<script src="//maps.googleapis.com/maps/api/js?key=' . $this->getGoogleMapsApiKey() . '" async defer></script>';
        }

        return '';
    }
}
