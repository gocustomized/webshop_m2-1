<?php
namespace CustomConcepts\Base\ViewModel;


class PriceViewModel implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    protected $pricingHelper;
    protected $priceCurrency;

    public function __construct(
        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
    ){
        $this->pricingHelper = $pricingHelper;
        $this->priceCurrency = $priceCurrency;
    }

    public function getPricingHelper(){
        return $this->pricingHelper;
    }

    public function getPriceCurrency(){
        return $this->priceCurrency;
    }
}
