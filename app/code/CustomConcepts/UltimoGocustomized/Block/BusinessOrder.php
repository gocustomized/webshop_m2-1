<?php

namespace CustomConcepts\UltimoGocustomized\Block;

class BusinessOrder extends \Magento\Contact\Block\ContactForm {

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var Product
     */
    private $product;

    public function __construct(
    \Magento\Framework\View\Element\Template\Context $context, \Magento\Framework\Registry $registry, array $data = array()
    ) {
        $this->registry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * @return Product
     */
    private function getProduct() {
        if (is_null($this->product)) {
            $this->product = $this->registry->registry('product');

            if (!$this->product->getId()) {
                throw new LocalizedException(__('Failed to initialize product'));
            }
        }

        return $this->product;
    }

    public function getProductName() {
        return $this->getProduct()->getName();
    }

//    public function __construct(
//        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
//        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
//        \Magento\Catalog\Helper\Image $imageHelper,
//        \Magento\Catalog\Block\Product\Context $context,
//        \CustomConcepts\UltimoGocustomized\Helper\Data $helper,
//        array $data = array()
//    ) {
//        $this->_categoryFactory = $categoryFactory;
//        $this->_imageHelper = $imageHelper;
//        $this->helper = $helper;
//        $this->_productCollectionFactory = $productCollectionFactory;
//        parent::__construct($context, $data);
//    }
}
