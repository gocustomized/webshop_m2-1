<?php


namespace CustomConcepts\Base\Block;

use Infortis\Base\Block\Product\View;
use Infortis\Base\Helper\Data as HelperData;
use Infortis\Base\Helper\Template\Catalog\Product\View as HelperTemplateProductView;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context;

class ProductView extends View
{
    protected $priceCurrency;
    protected $pricingHelper;
    protected $reviewSummaryFactory;

    public function __construct(
        Context $context,
        HelperData $helperData,
        HelperTemplateProductView $helperTemplateProductView,
        Registry $registry,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
        \Magento\Review\Model\ReviewSummaryFactory $reviewSummaryFactory,
        array $data = []
    ){
        $this->priceCurrency = $priceCurrency;
        $this->pricingHelper = $pricingHelper;
        $this->reviewSummaryFactory = $reviewSummaryFactory;
        parent::__construct($context, $helperData, $helperTemplateProductView, $registry, $data);
    }

    public function getProductTitle()
    {
        return $this->getProduct()->getName();
    }

    public function getProductPrice()
    {
        $product = $this->getProduct();

        if ($product->getTypeId() == Configurable::TYPE_CODE) {
            return $product->getFinalPrice();
        }

        return $product->getPrice();
    }

    public function getProductFormattedPrice()
    {
        $price = $this->getProductPrice();

        if($this->getProduct()->getTypeId() == Configurable::TYPE_CODE){
            return $this->priceCurrency->format($price, false);
        } else {
            return $this->pricingHelper->currency($price, true, false);
        }
    }

    public function getProductDescription()
    {
        return $this->getProduct()->getDescription();
    }

    public function setRatingSummaryData($product = null){
        if(!$product){
            $product = $this->getProduct();
        }

        if ($product->getRatingSummary() === null) {
            $this->reviewSummaryFactory->create()->appendSummaryDataToObject(
                $product,
                $this->_storeManager->getStore()->getId()
            );
        }

        $this->setRatingSummary($product->getRatingSummary());
        $this->setReviewsCount($product->getReviewsCount());
    }
}
