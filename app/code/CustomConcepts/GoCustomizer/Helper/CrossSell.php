<?php
namespace CustomConcepts\GoCustomizer\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

class CrossSell extends \Magento\Framework\App\Helper\AbstractHelper {

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;
    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $pricingHelper;
    /**
     * @var \Magento\Checkout\Helper\Cart
     */
    protected $cartHelper;
    /**
     * @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable
     */
    protected $configurable;
    /**
     * @var \Magento\Catalog\Model\Product\Attribute\Source\Status
     */
    protected $productStatus;
    /**
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $productVisibility;
    /**
     * @var \Magento\Catalog\Block\Product\ListProduct
     */
    protected $listBlock;

    protected $priceCurrency;

    /**
     * CrossSell constructor.
     * @param Context $context
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Store\Model\StoreManagerInterface $storeManagerInterface
     * @param \Magento\Framework\Pricing\Helper\Data $pricingHelper
     * @param \Magento\Checkout\Helper\Cart $cartHelper
     * @param \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurable
     * @param \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus
     * @param \Magento\Catalog\Model\Product\Visibility $productVisibility
     * @param \Magento\Catalog\Block\Product\ListProduct $listBlock
     */
    public function __construct(
        Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
        \Magento\Checkout\Helper\Cart $cartHelper,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurable,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus,
        \Magento\Catalog\Model\Product\Visibility $productVisibility,
        \Magento\Catalog\Block\Product\ListProduct $listBlock,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
    ){
        $this->checkoutSession = $checkoutSession;
        $this->productRepository = $productRepository;
        $this->storeManager = $storeManagerInterface;
        $this->pricingHelper = $pricingHelper;
        $this->cartHelper = $cartHelper;
        $this->configurable = $configurable;
        $this->productStatus = $productStatus;
        $this->productVisibility = $productVisibility;
        $this->listBlock = $listBlock;
        $this->priceCurrency = $priceCurrency;
        parent::__construct($context);
    }

    /**
     * @param bool $sku
     * @param bool $ajax
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCrossSellProducts($sku=false, $ajax=true){
        $cartItems = $this->getCartItems();

        $_product = $this->productRepository->get($sku);

        $crossselll_product_collection = $_product->getCrossSellProductCollection()
            ->addAttributeToFilter('status', ['in' => $this->productStatus->getVisibleStatusIds()])
            ->addAttributeToSelect('price')
            ->addAttributeToSelect('image')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('crosssell_description')
            ->setVisibility($this->productVisibility->getVisibleInSiteIds())
            ->setOrder('price', 'DESC')
            ->addStoreFilter($this->storeManager->getStore());

        $crosssell = [];
        foreach($crossselll_product_collection as $_product_crosssell)
        {
            $pdt_sku = $_product_crosssell->getSku();

            if(in_array($pdt_sku, $cartItems))
            {
                continue;
            }

            $crosssell[$pdt_sku]['id']          = $_product_crosssell->getId();
            $crosssell[$pdt_sku]['title']       = $_product_crosssell->getName();
            $crosssell[$pdt_sku]['price']       = '<span class="price">' . $this->priceCurrency->format($_product_crosssell->getPriceInfo()->getPrice('final_price')->getValue(), false) . '</span>';
            $crosssell[$pdt_sku]['image_url']   = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product' .$_product_crosssell->getImage();
            $crosssell[$pdt_sku]['type_id']     = $_product_crosssell->getTypeId();
            $crosssell[$pdt_sku]['description'] = $_product_crosssell->getCrosssellDescription();
            $crosssell[$pdt_sku]['add_to_cart_url'] = $this->cartHelper->getAddUrl($_product_crosssell);
            if($crosssell[$pdt_sku]['type_id'] == 'configurable' )
            {
                $crosssell[$pdt_sku]['list'] = $this->getSimpleProduct($_product_crosssell);
                if($crosssell[$pdt_sku]['list'] == '')
                {
                    unset($crosssell[$pdt_sku]);
                }
            }
        }

        return $crosssell;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCartItems(){
        $cartItems = [];
        $items = $this->checkoutSession->getQuote()->getAllItems();
        foreach($items as $item) {
            $cartItem[] = $item->getSku();
        }
        return $cartItems;
    }

    /**
     * @param $_product
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getSimpleProduct($_product){
        $simple_collection = $this->configurable->getUsedProductCollection($_product)
            ->addAttributeToFilter('status','1')
            ->addAttributeToSelect('price')
            ->addAttributeToSelect('image_url')
            ->addAttributeToSelect('crosssell_description')
            ->addAttributeToSelect('name')
            ->addStoreFilter($this->storeManager->getStore());

        $cartItem = $this->getCartItems();

        foreach($simple_collection as $simple_product){
            if(strpos($simple_product->getName(),$_product->getName())!==false){
                $title = substr($simple_product->getName(), strlen($_product->getName()) + 3);
            }else{
                $title = $simple_product->getName();
            }
            $sku = $simple_product->getSku();
            if(in_array($sku, $cartItem))
            {
                return '';
            }

            $productList[$sku]['sku'] = $sku;
            $productList[$sku]['id'] = $simple_product->getId();
            $productList[$sku]['title'] = $title;
            $productList[$sku]['price']= '<span class="price">' . $this->pricingHelper->currency($simple_product->getPriceInfo()->getPrice('final_price')->getValue(), true, false) . '</span>';
            $productList[$sku]['image_url'] = $simple_product->getImageUrl();
            $productList[$sku]['description'] = $simple_product->getCrosssellDescription();
            $productList[$sku]['add_to_cart_url'] = $this->cartHelper->getAddUrl($simple_product);
        }
        return $productList;
    }

    public function sortCrosssells($crosssells, $isArray = false){
        $sortOrder = $this->scopeConfig->getValue('crosssell/general/sort_order', ScopeInterface::SCOPE_STORE);
        $sortOrder = explode(',', $sortOrder);

        $sortedCrosssells = [];
        foreach ($sortOrder as $order){ //loop to sort crosssells
            foreach ($crosssells as $key => $crosssell){
                if($isArray){
                    $productId = $crosssell['id'];
                } else {
                    $productId = $crosssell->getId();
                }
                if($productId == $order){
                    if($isArray){
                        $sortedCrosssells[$key] = $crosssell;
                    } else {
                        $sortedCrosssells[] = $crosssell;
                    }
                    unset($crosssells[$key]);
                }
            }
        }

        foreach ($crosssells as $key => $crosssell){ //loop to add items that are not included in the sorting
            if($isArray){
                $sortedCrosssells[$key] = $crosssell;
            } else {
                $sortedCrosssells[] = $crosssell;
            }
        }

        return $sortedCrosssells;
    }


}
