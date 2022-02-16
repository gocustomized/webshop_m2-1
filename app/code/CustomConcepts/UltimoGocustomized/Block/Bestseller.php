<?php
namespace CustomConcepts\UltimoGocustomized\Block;

class Bestseller extends \Magento\Catalog\Block\Product\AbstractProduct
{
    protected $_productCollectionFactory;
    protected $_categoryFactory;
    protected $_imageHelper;

    
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Catalog\Block\Product\Context $context,
        \CustomConcepts\UltimoGocustomized\Helper\Data $helper,
        array $data = array()
    ) {
        $this->_categoryFactory = $categoryFactory;
        $this->_imageHelper = $imageHelper;
        $this->helper = $helper;
        $this->_productCollectionFactory = $productCollectionFactory;
        parent::__construct($context, $data);
    }
    
    public function getProductIds() {
        $productIDs = $this->helper->getConfig(\CustomConcepts\UltimoGocustomized\Helper\Data::XML_PATH_BESTSELLER_PRODUCTS);
        $trimedProductIds = array_map('trim', explode(',',$productIDs));
        return $trimedProductIds;
    }

    public function getImageHelper()
    {
        return $this->_imageHelper;
    }
    public function getProductCollection()
    {
        $collection = $this->_productCollectionFactory->create()
        ->addAttributeToSelect('*')
        ->addFieldToFilter('entity_id',['in' => $this->getProductIds()])
        ->addAttributeToFilter('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
        ->addAttributeToFilter('status',\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
        return $collection;
    }
   
   
}