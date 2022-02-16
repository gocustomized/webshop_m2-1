<?php

namespace CustomConcepts\GoCustomizer\ViewModel;


class GocustomizerViewModel implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    protected $gcHelper;
    protected $productRepository;
    protected $ccPriceHelper;
    protected $storeManager;

    public function __construct(
        \CustomConcepts\GoCustomizer\Helper\Data $gcHelper,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \CustomConcepts\Base\Helper\Price $ccPriceHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ){
        $this->gcHelper = $gcHelper;
        $this->productRepository = $productRepository;
        $this->ccPriceHelper = $ccPriceHelper;
        $this->storeManager = $storeManager;
    }

    /**
     * @return \CustomConcepts\GoCustomizer\Helper\Data
     */
    public function getGcHelper(){
        return $this->gcHelper;
    }

    public function getProductById($productId){
        return $this->productRepository->getById($productId);
    }

    /**
     * @return \CustomConcepts\Base\Helper\Price
     */
    public function getCcPriceHelper(){
        return $this->ccPriceHelper;
    }

    public function getStoreManager(){
        return $this->storeManager;
    }

    public function getTranslatedProduct($productId){
        return $this->productRepository->getById($productId, false, $this->storeManager->getStore()->getId());
    }
}
