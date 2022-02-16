<?php

/**
 * CustomConcepts_OrderExport extension
 * @category  CustomConcepts_Extensions
 * @package   CustomConcepts_OrderExport
 * @copyright Copyright (c) 2017
 * @author GoCustomized <info@gocustomized.com>
 */

namespace CustomConcepts\OrderExport\Block\Adminhtml\OrderExport;
use Magento\Backend\Block\Widget\Context;

class Edit extends \Magento\Backend\Block\Widget\Form\Container {

    //put your code here
    /**
     * @param Context $context
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
    Context $context, array $data = []
    ) {

        parent::__construct($context, $data);
    }

    /**
     * Class constructor
     *
     * @return void
     */
    protected function _construct() {
        $this->_objectId = 'id';
        $this->_blockGroup = 'CustomConcepts_OrderExport';
        $this->_controller = 'adminhtml_orderExport';
        parent::_construct();

        $this->buttonList->update('save', 'label', __('Export Orders'));
    }

    /**
     * Retrieve text for header element depending on loaded news
     * 
     * @return string
     */
    public function getHeaderText() {

        return __('Download Orders');
    }

}
