<?php

/**
 * CustomConcepts_ExcelImportOrders extension
 * @category  CustomConcepts_Extensions
 * @package   CustomConcepts_ExcelImportOrders
 * @copyright Copyright (c) 2017
 * @author GoCustomized <info@gocustomized.com>
 */
namespace CustomConcepts\ExcelImportOrders\Controller\Adminhtml\Importer;

class ImportOrders extends \Magento\Backend\App\Action {
    
    /**
     *
     * @var type \Magento\Framework\File\UploaderFactory
     */
    protected $uploaderFactory;
    /**
     *
     * @var type \CustomConcepts\ExcelImportOrders\Model\ImportordersFactory
     */
    protected $excelImportOrdersImportordersFactory;
    /**
     *
     * @var type \Magento\Framework\Session\GenericFactory
     */
    protected $genericFactory;
    /**
     *
     * @var type $context->getSession();
     */
    protected $backendSession;
    
    /**
     *
     * @var type \Magento\Framework\App\Filesystem\DirectoryList
     */
    protected $directoryList;
    
    /**
     * 
     * @param \Magento\Backend\App\Action\Context $context
     * @param \CustomConcepts\ExcelImportOrders\Model\ImportordersFactory $excelImportOrdersImportordersFactory
     * @param \Magento\Framework\Session\GenericFactory $genericFactory
     * @param \Magento\Framework\File\UploaderFactory $uploaderFactory
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directoryList
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context ,
        \CustomConcepts\ExcelImportOrders\Model\ImportordersFactory $excelImportOrdersImportordersFactory,
        \Magento\Framework\Session\GenericFactory $genericFactory, 
        \Magento\Framework\File\UploaderFactory $uploaderFactory ,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList
    ) {
        $this->uploaderFactory = $uploaderFactory;
        $this->excelImportOrdersImportordersFactory = $excelImportOrdersImportordersFactory;
        $this->genericFactory = $genericFactory;
        $this->backendSession = $context->getSession();
        $this->directoryList = $directoryList;
        parent::__construct($context);
    }
    
    public function _isAllowed() {
        return $this->_authorization->isAllowed('CustomConcepts_ExcelImportOrders::excelimport');
    }
    public function execute() {
        if ($this->getRequest()->getFiles('order_xls')['name'] != '') {
            try {
                $uploader = $this->uploaderFactory->create(['fileId' => 'order_xls']);
                $uploader->setAllowedExtensions(array('xls'));
                $uploader->setAllowRenameFiles(false);
                $uploader->setFilesDispersion(false);
                $path = $this->directoryList->getPath('var') . DIRECTORY_SEPARATOR . 'ordersimport/import/';
                $uploader->save($path, $this->getRequest()->getFiles('order_xls')['name']);
                $this->excelImportOrdersImportordersFactory->create()->readExcel($path . str_replace(' ', '_',$this->getRequest()->getFiles('order_xls')['name']));
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError(__('Invalid file type!!'));
                //$this->messageManager->addError(__($e->getMessage()));
            }
            
            unlink($this->directoryList->getPath('var') . DIRECTORY_SEPARATOR. 'ordersimport/import/' . str_replace(' ', '_',$this->getRequest()->getFiles('order_xls')['name']));
            $this->_redirect('*/*/');
        } else {
            $this->messageManager->addError(__('Unable to find the import file'));
            $this->_redirect('*/*/');
        }
    }
}