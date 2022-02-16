<?php

/**
 * CustomConcepts_ExtendedDropshipment extension
 * @category  CustomConcepts_Extensions
 * @package   CustomConcepts_ExtendedDropshipment
 * @copyright Copyright (c) 2017
 * @author GoCustomized <info@gocustomized.com>
 */

namespace CustomConcepts\ExtendedDropshipment\Controller\Adminhtml\Index;

use Magento\Framework\Controller\ResultFactory;

class Index extends \Magento\Backend\App\Action {

    /**
     * 
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(\Magento\Backend\App\Action\Context $context) {
        parent::__construct($context);
    }

    /*
     * ACL check
     */

    public function _isAllowed() {
        return $this->_authorization->isAllowed('CustomConcepts_ExtendedDropshipment::dropshipment');
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute() {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Dropshipments'));
        return $resultPage;
    }

}
