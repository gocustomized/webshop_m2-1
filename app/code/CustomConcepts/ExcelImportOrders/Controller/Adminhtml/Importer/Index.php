<?php

/**
 * CustomConcepts_ExcelImportOrders extension
 * @category  CustomConcepts_Extensions
 * @package   CustomConcepts_ExcelImportOrders
 * @copyright Copyright (c) 2017
 * @author GoCustomized <info@gocustomized.com>
 */
namespace CustomConcepts\ExcelImportOrders\Controller\Adminhtml\Importer;

class Index extends \Magento\Backend\App\Action {

    /**
     * 
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(\Magento\Backend\App\Action\Context $context) {
        parent::__construct($context);
    }
    
    public function _isAllowed() {
        return $this->_authorization->isAllowed('CustomConcepts_ExcelImportOrders::excelimport');
    }

    public function execute() {
      $this->_view->loadLayout()
                ->renderLayout();
    }
}

