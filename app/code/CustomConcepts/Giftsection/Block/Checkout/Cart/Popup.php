<?php

namespace CustomConcepts\Giftsection\Block\Checkout\Cart;

use Magento\CatalogInventory\Helper\Stock as StockHelper;
use Magento\Store\Model\ScopeInterface;
use Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable;
use Magento\Catalog\Model\ProductFactory;

class Popup extends \Magento\Checkout\Block\Cart\Crosssell {

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;
    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $pricingHelper;
    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceCurrency;
    /**
     * @var \CustomConcepts\Base\Helper\Price
     */
    protected $ccPricingHelper;

    /**
     * Popup constructor.
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Catalog\Model\Product\Visibility $productVisibility
     * @param \Magento\Catalog\Model\Product\LinkFactory $productLinkFactory
     * @param \Magento\Quote\Model\Quote\Item\RelatedProducts $itemRelationsList
     * @param StockHelper $stockHelper
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Pricing\Helper\Data $pricingHelper
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \CustomConcepts\Base\Helper\Price $ccPricingHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Catalog\Model\Product\Visibility $productVisibility,
        \Magento\Catalog\Model\Product\LinkFactory $productLinkFactory,
        \Magento\Quote\Model\Quote\Item\RelatedProducts $itemRelationsList,
        StockHelper $stockHelper,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \CustomConcepts\Base\Helper\Price $ccPricingHelper,
        array $data = []
    ){
        parent::__construct($context, $checkoutSession, $productVisibility, $productLinkFactory, $itemRelationsList, $stockHelper, $data);
        $this->productRepository = $productRepository;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->request = $request;
        $this->pricingHelper = $pricingHelper;
        $this->priceCurrency = $priceCurrency;
        $this->ccPricingHelper = $ccPricingHelper;
    }

    /**
     * @return array list of gift products
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getGifts()
    {
        $store = $this->storeManager->getStore();

        $cartProductIds = $this->_getCartProductIds();
        $filteredGiftItemInCartProductIds = [];

        foreach ($cartProductIds as $cartProductId){
            $product = $this->productRepository->getById($cartProductId);
            if ($product->getShowInGiftPopup()) {
                $filteredGiftItemInCartProductIds[] = $product->getEntityId();
            }
        }

        $collection = $this->_getCollection();
        if ($filteredGiftItemInCartProductIds) {
            $collection->addAttributeToFilter('entity_id', array('nin' => $filteredGiftItemInCartProductIds));
        }
        
        $collection->addAttributeToFilter('show_in_gift_popup', array('eq' => 1))
            ->addAttributeToSelect('price')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('short_description')
            ->addAttributeToSelect('gift_item_position')
            ->addAttributeToSelect('show_in_gift_popup')
            ->addAttributeToSelect('image')
            ->addStoreFilter($store)
            ->groupByAttribute('entity_id')
            ->setOrder('gift_item_position', 'ASC')
            ->setPageSize(0)
            ->load();

        $gifts = [];
        foreach($collection as $product){
            $image_url = $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product' .$product->getImage();
            $gifts[$product->getSku()]['type']                 = 'gift';
            $gifts[$product->getSku()]['title']                = $product->getName();
            $gifts[$product->getSku()]['price']                = $this->priceCurrency->format($product->getPrice(), false);
            $gifts[$product->getSku()]['special_price']        = $this->ccPricingHelper->hasSpecialPrice($product) ? $this->ccPricingHelper->getFormattedSpecialPrice($product) : 0;
            $gifts[$product->getSku()]['image_url']            = $image_url;
            $gifts[$product->getSku()]['description']          = $product->getShortDescription();
            $gifts[$product->getSku()]['add_to_cart_url']      = $this->getUrl('giftsection/*/add');
        }

        return $gifts;
    }

    /**
     * @return array notecard values
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getNotecard(){
        $store = $this->storeManager->getStore();
        $notecard = $this->productRepository->get('91324100706', false, $store->getId()); //id 2972

        return array(
            'sku'  => $notecard->getSku(),
            'type' => 'notecard',
            'title' => $notecard->getName(),
            'price' => $this->priceCurrency->format($notecard->getPrice(), false),
            'special_price' => $this->ccPricingHelper->hasSpecialPrice($notecard) ? $this->ccPricingHelper->getFormattedSpecialPrice($notecard) : 0,
            'image_url' => $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product' .$notecard->getImage(),
            'description' => $notecard->getShortDescription(),
            'add_to_cart_url' => $this->getUrl('giftsection/cart/add')
        );
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getEditData(){
        $quoteItemId = $this->request->getParam('quote_item');

        if($quoteItemId){
            $data = unserialize($this->_checkoutSession->getQuote()->getItemById($quoteItemId)->getGocustomizerData());
            if(isset($data['text'])){
                return $data['text'];
            }
        }


    }

    /**
     * @return mixed
     */
    public function isAutoshow(){
        return $this->scopeConfig->getValue('commonconfig/gift_section/auto_show');
    }
}
