<?php
namespace CustomConcepts\UltimoGocustomized\Model\Config\Source;

class CategoryModel implements \Magento\Framework\Option\ArrayInterface
{
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $collectionFactory
    ) {
         $this->_collectionFactory = $collectionFactory;
    }
    public function toOptionArray()
    {
        $_categories     = $this->_collectionFactory->create()->addAttributeToSelect('*');
        $optionsValue[] = ['label' =>'-SELECT CATEGORY-', 'value'=>'', 'selected'=>'selected','style'=>'font-weight:bold;','disabled'=>'disabled'];
        foreach($_categories as $category){
            $optionsValue[] = ['label' => $category->getName(), 'value' => $category->getId()];
        }
        return $optionsValue;
    }
}