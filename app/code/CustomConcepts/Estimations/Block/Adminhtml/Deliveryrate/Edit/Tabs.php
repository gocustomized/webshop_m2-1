<?php
namespace CustomConcepts\Estimations\Block\Adminhtml\Deliveryrate\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected function _construct()
    {

        parent::_construct();
        $this->setId('checkmodule_deliveryrate_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Deliveryrate Information'));
    }
}
