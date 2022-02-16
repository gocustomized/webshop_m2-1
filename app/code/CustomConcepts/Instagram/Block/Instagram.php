<?php
/**
 * CustomConcepts Infotech
 * CustomConcepts Instagram Extension
 * 
 * @category   CustomConcepts
 * @package    CustomConcepts_Instagram
 * @copyright  Copyright © 2006-2016 CustomConcepts
 *  
 */
namespace CustomConcepts\Instagram\Block;

use Magento\Framework\View\Element\Template;

class Instagram extends Template {
    
    
    protected function _prepareLayout() {
        
        parent::_prepareLayout();
        $this->pageConfig->getTitle()->set(__('Instagram'));
    }
    

}