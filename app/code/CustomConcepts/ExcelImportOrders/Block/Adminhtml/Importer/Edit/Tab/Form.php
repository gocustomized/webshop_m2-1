<?php

/**
 * CustomConcepts_ExcelImportOrders extension
 * @category  CustomConcepts_Extensions
 * @package   CustomConcepts_ExcelImportOrders
 * @copyright Copyright (c) 2017
 * @author GoCustomized <info@gocustomized.com>
 */

namespace CustomConcepts\ExcelImportOrders\Block\Adminhtml\Importer\Edit\Tab;

use Magento\Framework\UrlInterface;
class Form extends \Magento\Backend\Block\Widget\Form {

    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $backendSession;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\Data\FormFactory
     */
    protected $formFactory;

    /**
     *
     * @var type \Magento\Framework\UrlInterface
     */
    protected $urlInterface;
    /**
     * 
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Model\Session $backendSession
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Framework\UrlInterface $urlInterface
     * @param array $data
     */
    public function __construct(
    \Magento\Backend\Block\Template\Context $context,  \Magento\Framework\Registry $registry, \Magento\Framework\Data\FormFactory $formFactory, array $data = array()
    ) {
        $this->formFactory = $formFactory;
        $this->backendSession = $context->getBackendSession();  //$backendSession;
        $this->registry = $registry;
        $this->urlInterface =  $context->getUrlBuilder();  //$urlInterface;
        parent::__construct($context, $data);
    }

    protected function _prepareForm() {
        
        $form = $this->formFactory->create([
            'data' =>[
                'id' => 'config_form',
                'action' => $this->getUrl('*/*/importOrders'),
                'method' => 'post',
                'enctype' => 'multipart/form-data'
            ]
        ]);
        $form->setUseContainer(true);
        $this->setForm($form);
        $form->setHtmlIdPrefix('page_');
        $fieldset = $form->addFieldset('importer_form', array('legend' => __('Import Orders')));
        $xls_url = '';
        //$xls_url = Mage::getBaseUrl(\Magento\Store\Model\Store::URL_TYPE_MEDIA).'importorders/excel_sheet_sample_admin.xls';	
        $xls_url = $this->urlInterface->getBaseUrl(['_type' => UrlInterface::URL_TYPE_MEDIA ]).'importorders/excel_sheet_sample_admin.xls';	
        $fieldset->addField('sample_link', 'link', array(
            'label' => 'Download sample excel :',
            'href' => $xls_url,
            'value' => 'Download sample excel'
        ));

        $fieldset->addField('order_xls', 'file', array(
            'label' => __('Orders Excel File :'),
            'required' => true,
            'name' => 'order_xls',
            'validate_class' => 'required',
        ));

        $fieldset->addField('submit', 'submit', array(
            'value' => 'Import',
            'after_element_html' => '<small></small>',
            'class' => 'form-button button primary abs-action-primary',
            'tabindex' => 1
           
        ));

        if ($this->backendSession->getImporterData()) {
            $form->setValues($this->backendSession->getImporterData());
            $this->backendSession->setExporterData(null);
        } elseif ($this->registry->registry('importer_data')) {
            $form->setValues($this->registry->registry('importer_data')->getData());
        }
        return parent::_prepareForm();
    }

}
