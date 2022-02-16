<?php

/**
 * CustomConcepts_OrderExport extension
 * @category  CustomConcepts_Extensions
 * @package   CustomConcepts_OrderExport
 * @copyright Copyright (c) 2017
 * @author GoCustomized <info@gocustomized.com>
 */

namespace CustomConcepts\OrderExport\Block\Adminhtml\OrderExport\Edit;

class Form extends \Magento\Backend\Block\Widget\Form\Generic {

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
     * @var type \Magento\Store\Model\System\Store $systemStore
     */
    protected $_systemStore;

    /**
     * @var \Xtento\OrderExport\Helper\Entity
     */
    protected $eavConfig;

    /**
     * Order config
     *
     * @var \Magento\Sales\Model\Order\ConfigFactory
     */
    protected $_orderConfig;

    /**
     * 
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Sales\Model\Order\ConfigFactory $orderConfig
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param array $data
     */
    public function __construct(
    \Magento\Backend\Block\Template\Context $context, \Magento\Sales\Model\Order\ConfigFactory $orderConfig, \Magento\Store\Model\System\Store $systemStore, \Magento\Framework\Registry $registry, \Magento\Framework\Data\FormFactory $formFactory, \Magento\Eav\Model\Config $eavConfig, array $data = array()
    ) {
        $this->_orderConfig = $orderConfig;
        $this->formFactory = $formFactory;
        $this->eavConfig = $eavConfig;
        $this->backendSession = $context->getBackendSession();  //$backendSession;
        $this->registry = $registry;
        $this->_systemStore = $systemStore;
        $this->urlInterface = $context->getUrlBuilder();  //$urlInterface;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Init class
     *
     * @return void
     */
    protected function _construct() {
        parent::_construct();

        $this->setId('exportordersForm');
        $this->setTitle(__('Export Orders'));
    }

    /**
     * @return form
     */
    protected function _prepareForm() {

        $form = $this->_formFactory->create(
                ['data' => ['id' => 'edit_form', 'action' => $this->getUrl('*/*/exportOrders'), 'method' => 'post']]
        );

        $fieldset = $form->addFieldset(
                'base_fieldset', ['legend' => __('Export Orders'), 'class' => 'fieldset-wide']
        );

        $dateFormat = $this->_localeDate->getDateFormat(
                \IntlDateFormatter::SHORT
        );

        if (!$this->_storeManager->isSingleStoreMode()) {
            $field = $fieldset->addField(
                    'stores', 'multiselect', [
                'name' => 'stores[]',
                'label' => __('Store View'),
                'title' => __('Store View'),
                'required' => true,
                'values' => $this->_systemStore->getStoreValuesForForm(false, true)
                    ]
            );
            $renderer = $this->getLayout()->createBlock(
                    'Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element'
            );
            $field->setRenderer($renderer);
        } else {
            $fieldset->addField(
                    'stores', 'hidden', ['name' => 'stores[]', 'value' => $this->_storeManager->getStore(true)->getId()]
            );
        }

        $fieldset->addField(
                'from_date', 'date', [
            'name' => 'from_date',
            'label' => __('From Date'),
            'required' => true,
            'date_format' => $dateFormat,
            'class' => 'validate-date validate-date-range date-range-custom_theme-from'
                ]
        );
        $fieldset->addField(
                'to_date', 'date', [
            'name' => 'to_date',
            'label' => __('To Date'),
            'required' => true,
            'date_format' => $dateFormat,
            'class' => 'validate-date validate-date-range date-range-custom_theme-from'
                ]
        );
        $fieldset->addField(
                'client_id', 'text', [
            'name' => 'client_id',
            'label' => __('Client Id'),
            'class' => 'validate-number'
                ]
        );
        //fetch all suppliers from EAV
        $attribute = $this->eavConfig->getAttribute('catalog_product', 'suppliers');
        $suppliersArray = $attribute->getSource()->getAllOptions();
        foreach ($suppliersArray as $supplier) {
            if ($supplier['value'] != '')
                $values[] = ['label' => __($supplier['label']), 'value' => $supplier['value']];
        }
        $fieldset->addField(
                'supplier', 'multiselect', [
            'name' => 'supplier',
            'label' => __('Supplier'),
            'values' => $values,
            'required' => true,
            'class' => 'required-entry',
                ]
        );
        
        //Fetch all order status
        $statuses = $this->_orderConfig->create()->getStatuses();
        $values = [];
        foreach ($statuses as $code => $label) {
            $values[] = ['label' => __($label), 'value' => $code];
        }

        $fieldset->addField(
                'order_statuses', 'multiselect', [
            'name' => 'order_statuses',
            'label' => __('Order Status'),
            'values' => $values,
            'required' => true,
            'class' => 'required-entry',
                ]
        );

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

}
