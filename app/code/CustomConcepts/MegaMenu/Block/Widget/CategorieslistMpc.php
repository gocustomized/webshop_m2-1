<?php
namespace CustomConcepts\MegaMenu\Block\Widget;

class CategorieslistMpc extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{
    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _toHtml(){
//        $parentId = (int)str_replace('category/','',$this->getData('parent_id'));
        $parentId = str_replace('category/','',$this->getData('parent_id'));
        $categoriesTree = $this->getLayout()->createBlock('CustomConcepts\MegaMenu\Block\Widget\CategoriesTreeMpc');
//        $categoriesTree->setData('parent_id',$parentId);
        if($this->getData('item_count')){
            $categoriesTree->setData('item_count',$this->getData('item_count'));
        }
        if($this->getData('include_products')){
            $categoriesTree->setData('include_products',$this->getData('include_products'));
        }
        if($this->getData('include_product_image')){
            $include_product_image = $this->getData('include_product_image') === 'true'? true: false;
            $categoriesTree->setData('include_product_image',$include_product_image);
        } else {
            $categoriesTree->setData('include_product_image',true);
        }
        if($this->getData('max_level')){
            $categoriesTree->setData('max_level',$this->getData('max_level'));
        }
        if($this->getData('category_filter')){
            $categoriesTree->setData('category_filter',$this->getData('category_filter'));
        }

        $parentIds = explode(',', $parentId);

        if($this->_storeManager->getWebsite()->getCode() == 'custom_concepts'){ //MPC menu integration
            $categoryHtml = '';
            $productHtml = '';
            foreach ($parentIds as $parent){
                $categoriesTree->setData('parent_id',$parent);
                $categoryHtml .= $categoriesTree->getHtml('', 'submenu', 0);
                $productHtml .= $categoriesTree->productHtml;
                $categoriesTree->productHtml = '';
            }

            $html = '';

            $html .= '<div class="product-section desktop-view">';

            $menuTextHeader = __('Please choose your phone type');
            $menuTextContent = __('Life is a matter of choices and every choice you make, makes you.');

            $html .= '<div class="menutextholder">
                         <div class="textinnerholder">
                            <h1>'. $menuTextHeader .'</h1>
                            <img src="' . $this->getViewFileUrl("images/icons8-long-arrow-left-filled-100.png") . '" loading="lazy"/>
                            <p>"'. $menuTextContent .'"</p>
                         </div>
                      </div>';

            if($categoriesTree->getData('include_product_image')){
                $imageClass = 'menu-place-holder';
            } else {
                $imageClass = 'menu-place-holder-others';
            }
            $html .= '<div class="menuimgholder">
                           <div class="innerimgholder">
                                <img class="' . $imageClass . '" src="' . $this->getViewFileUrl("images/PlaceHolder.png") . '" loading="lazy"/>
                            </div>
                      </div>';

            $html .= $productHtml;
            $html .= '</div>';


//            $html .= '<div class="nav-block--center grid12-3">';
            $html .= '<ul class="level0 nav-submenu">';

            $html .= $categoryHtml;

            $html .= '</ul>';
//            $html .= '</div>';

            return $html;
        } else {
            return '<ul class="level0 nav-submenu">'.$categoriesTree->getHtml('', 'submenu', 0).'</ul>';
        }

    }
}
