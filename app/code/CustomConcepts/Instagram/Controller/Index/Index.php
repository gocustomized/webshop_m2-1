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
namespace CustomConcepts\Instagram\Controller\Index;

use Magento\Framework\App\RequestInterface;

class Index extends \Magento\Framework\App\Action\Action
{

    public function execute() {
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->renderLayout();  
    }

}