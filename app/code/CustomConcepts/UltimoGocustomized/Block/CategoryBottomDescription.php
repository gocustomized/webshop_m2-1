<?php
namespace CustomConcepts\UltimoGocustomized\Block;

class CategoryBottomDescription extends \Magento\Catalog\Block\Product\AbstractProduct
{
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = array()
    ) {
        $this->registry = $registry;
        parent::__construct($context, $data);
    }
    
    public function getCurrentCategoryDescription() {
        $currentCategory = $this->registry->registry('current_category');
        return $currentCategory->getData('bottom_description');
    }
   
}