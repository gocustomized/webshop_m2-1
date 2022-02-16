<?php
namespace CustomConcepts\Checkout\Block\Cart;

use Magento\CatalogInventory\Helper\Stock as StockHelper;

class Crosssell extends \Magento\Checkout\Block\Cart\Crosssell
{
    protected $crossSellHelper;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Catalog\Model\Product\Visibility $productVisibility,
        \Magento\Catalog\Model\Product\LinkFactory $productLinkFactory,
        \Magento\Quote\Model\Quote\Item\RelatedProducts $itemRelationsList,
        StockHelper $stockHelper,
        \CustomConcepts\GoCustomizer\Helper\CrossSell $crossSellHelper,
        array $data = []
    ){
        $this->crossSellHelper = $crossSellHelper;
        parent::__construct($context, $checkoutSession, $productVisibility, $productLinkFactory, $itemRelationsList, $stockHelper, $data);
        //TODO: we can create a config for this.
        $this->_maxItemCount = 100;
    }

    public function getItems(){
        $items = parent::getItems();
        return $this->crossSellHelper->sortCrosssells($items);
    }
}
