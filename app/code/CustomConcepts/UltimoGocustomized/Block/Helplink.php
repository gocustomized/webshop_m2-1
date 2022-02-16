<?php
namespace CustomConcepts\UltimoGocustomized\Block;

class Helplink extends \Magento\Customer\Block\Account\Customer
{
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Customer\Model\Url $customerUrl,
        array $data = array()
    ) {
        $this->_customerUrl = $customerUrl;
        parent::__construct($context, $httpContext, $data);
    }
    
    public function getHelpLink()
    {
        return $this->getBaseUrl().'help';
    }
    public function getAccountLink()
    {
        return $this->_customerUrl->getAccountUrl();
    }
    
}