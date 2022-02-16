<?php

/**
 * CustomConcepts_GoCustomizer extension
 * @category  CustomConcepts_Extensions
 * @package   CustomConcepts_GoCustomizer
 * @copyright Copyright (c) 2017
 * @author GoCustomized <info@gocustomized.com>
 */

namespace CustomConcepts\GoCustomizer\Helper;

use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Helper\Image;

class Data extends \Magento\Framework\App\Helper\AbstractHelper {

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var CollectionFactory
     */
    protected $catalogResourceModelProductCollectionFactory;

    /**
     * @var Configurable
     */
    protected $productTypeConfigurable;

    /**
     * @var ProductFactory
     */
    protected $catalogProductFactory;

    /**
     * @var Image
     */
    protected $imageHelper;

    /**
     *
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param CollectionFactory $catalogResourceModelProductCollectionFactory
     * @param Configurable $productTypeConfigurable
     * @param ProductFactory $catalogProductFactory
     * @param Image $imageHelper
     */
    public function __construct(
        Context $context, StoreManagerInterface $storeManager, CollectionFactory $catalogResourceModelProductCollectionFactory, Configurable $productTypeConfigurable, ProductFactory $catalogProductFactory, Image $imageHelper
    ) {
        $this->storeManager = $storeManager;
        $this->catalogResourceModelProductCollectionFactory = $catalogResourceModelProductCollectionFactory;
        $this->productTypeConfigurable = $productTypeConfigurable;
        $this->catalogProductFactory = $catalogProductFactory;
        $this->imageHelper = $imageHelper;
        parent::__construct(
                $context
        );
    }

    /**
     * Get the Edit link for customizer
     * @param type $simpleproduct_id
     * @param type $item_id
     * @return URL
     */
    public function getCustomizerEditUrl($simpleproduct_id, $item_id) {
        return $this->_getUrl('gocustomizer/customizer/edit', ['product_id' => (int) $simpleproduct_id, 'item_id' => (int) $item_id]);
    }
    /**
     * Checks weather product is customizer product or not
     * @param type $item
     * @return boolean
     */
    public function isGocustomizerProduct($item) {

        $parentIds = $this->productTypeConfigurable->getParentIdsByChild($item->getProductId());

        if (!empty($parentIds) && isset($parentIds[0])) {
            $configurableProduct = $this->catalogProductFactory->create()->load($parentIds[0]);
            $this->_gocustomizer_product_id = (int) $configurableProduct->getData('gocustomizer_product_id');
            return (bool) $this->_gocustomizer_product_id;
        }
        return false;
    }

    /**
     * Get the product thumbnail from customizer data
     * @param type $item
     * @return type
     */
    public function getProductThumbnail($item) {
        $gocustomizerdata = unserialize($item->getGocustomizerData());
        if (!empty($gocustomizerdata['thumb'])) {
            return $gocustomizerdata['thumb'] . '?t=' . time();
        }
        
        return false;
    }

    /**
     * Get the product thumbnail from product thumbnail
     * @param type $item
     * @return type
     */
    public function getProductDefaultThumbnail($item) {
        return $this->imageHelper->init($item->getProduct(), 'product_thumbnail_image')->getUrl();
    }
    
    /**
     * Get the product design from customizer data
     * @param type $item
     * @return type
     */
    public function getProductDesign($item) {
        $gocustomizerdata = unserialize($item->getGocustomizerData());
        if (!empty($gocustomizerdata['design'])) {
            return $gocustomizerdata['design'];
        }
    }

    /**
     * @param $item
     * @return mixed
     */
    public function getProductDesignEngineUrl($item) {
        $gocustomizerdata = unserialize($item->getGocustomizerData());
        if (!empty($gocustomizerdata['design_engine_url'])) {
            return $gocustomizerdata['design_engine_url'];
        }
    }

    public function getProductSku($item){
        /** for order and quote items */
        if(is_a($item, 'Magento\Sales\Model\Order\Item') || is_a($item, 'Magento\Quote\Model\Quote\Item')) {
            $parentIds = $this->productTypeConfigurable->getParentIdsByChild($item->getProductId());
            if(empty($parentIds)) {
                $configurableProduct = $this->catalogProductFactory->create()->load($item->getProductId());
            }else {
                $configurableProduct = $this->catalogProductFactory->create()->load($parentIds[0]);
            }
        }else{ /** for normal products */
            $parentIds = $this->productTypeConfigurable->getParentIdsByChild($item->getProductId());
            if(empty($parentIds)){
                $configurableProduct = $this->catalogProductFactory->create()->load($item->getId());
            }else{
                $configurableProduct = $item;
            }
        }
        return $configurableProduct->getSku();
    }
}
