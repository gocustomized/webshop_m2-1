<?php
namespace CustomConcepts\Base\Block;

use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Registry;

class CrumbBlock extends \Magento\Framework\View\Element\Template
{

    public function __construct(
        Context $context,
        Registry $registry,
        array $data = []
    ){
        $this->registry = $registry;
        parent::__construct($context, $data);
    }

    public function getCrumbs()
    {
        $evercrumbs = array();

        $evercrumbs[] = array(
            'label' => __('Home'),
            'title' => __('Go to Home Page'),
            'link' => $this->_storeManager->getStore()->getBaseUrl()
        );

        $category = $this->registry->registry('current_category');
        if($category){
            $breadcrumbCategories = $category->getParentCategories();
            foreach ($breadcrumbCategories as $category) {
                $evercrumbs[] = array(
                    'label' => $category->getName(),
                    'title' => $category->getName(),
                    'link' => $category->getUrl()
                );
            }
        }

        $product = $this->registry->registry('current_product');
        if($product){
            $evercrumbs[] = array(
                'label' => $product->getName(),
                'title' => $product->getName(),
                'link' => ''
            );
        }

        return $evercrumbs;
    }
}
