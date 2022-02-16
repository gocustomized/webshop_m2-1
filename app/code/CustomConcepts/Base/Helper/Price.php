<?php
namespace CustomConcepts\Base\Helper;


use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\App\Helper\Context;

class Price extends \Magento\Framework\App\Helper\AbstractHelper
{
    /** @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface  */
    protected $date;
    /** @var \Magento\Framework\Pricing\Helper\Data  */
    protected $pricingHelper;

    /**
     * Price constructor.
     * @param Context $context
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date
     * @param \Magento\Framework\Pricing\Helper\Data $pricingHelper
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper
    ){
        $this->date = $date;
        $this->pricingHelper = $pricingHelper;
        parent::__construct($context);
    }

    /**
     * @param $product \Magento\Catalog\Model\Product
     * @return int
     */
    public function hasSpecialPrice($product){
        $specialprice = $product->getSpecialPrice();
        $specialfromdate = $product->getSpecialFromDate();
        $specialtodate = $product->getSpecialToDate();
        $today = $this->date->date()->format('Y-m-d 00:00:00');

        /** TODO: find a magento way of validating the special price */
//        if ($specialprice && $specialprice < $this->getOriginalPrice($product)) {
        if ($specialprice) {
            if ((is_null($specialfromdate) &&is_null($specialtodate)) ||
                (strtotime($today) >= strtotime($specialfromdate) && is_null($specialtodate)) ||
                (strtotime($today) <= strtotime($specialtodate) && is_null($specialfromdate)) ||
                (strtotime($today) >= strtotime($specialfromdate) && strtotime($today) <= strtotime($specialtodate)))
            {
                return 1;
            }
        }

        return 0;
    }

    /**
     * @param $product \Magento\Catalog\Model\Product
     * @return float|string
     */
    public function getFormattedSpecialPrice($product){
        return $this->pricingHelper->currency($product->getSpecialPrice(), true, false);
    }

    /**
     * @param $product \Magento\Catalog\Model\Product
     * @return mixed
     */
    public function getOriginalPrice($product)
    {
        return $product->getData('price');
    }

    /**
     * @param $product \Magento\Catalog\Model\Product
     * @return float|string
     */
    public function getFormattedOriginalPrice($product){
        return $this->pricingHelper->currency($this->getOriginalPrice($product), true, false);
    }

    public function getProductPriceHtml($product, $config = []){
        $containerClass = isset($config['containerClass']) ? $config['containerClass'] : ['price-box', 'flex-column'];
        $specialPriceLabel = isset($config['specialPriceLabel']) ? $config['specialPriceLabel'] . ' ' : '';

        $html = '<div class="' . implode(' ', $containerClass) . '" data-role="priceBox" data-product-id="' . $product->getId() . '" data-price-box="product-id-' . $product->getId() . '">';

        if($this->hasSpecialPrice($product)){
            $html .= '<span class="orig-price">' . $this->getFormattedOriginalPrice($product) . '</span>';
            $html .= '<span class="price special-price">' . $specialPriceLabel . '<span>' . $this->getFormattedSpecialPrice($product) . '</span></span>';
        } else {
            $html .= '<span class="price">' . $this->getFormattedOriginalPrice($product) . '</span>';
        }

        $html .= '</div>';

        return $html;
    }

    public function convertPriceToCurrency($price){
        return $this->pricingHelper->currency($price, true, false);
    }
}
