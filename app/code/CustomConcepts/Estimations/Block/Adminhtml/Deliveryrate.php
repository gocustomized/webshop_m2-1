<?php
namespace CustomConcepts\Estimations\Block\Adminhtml;
class Deliveryrate extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {

        $this->_controller = 'adminhtml_deliveryrate';/*block grid.php directory*/
        $this->_blockGroup = 'CustomConcepts_Estimations';
        $this->_headerText = __('DeliveryRate');
        $this->_addButtonLabel = __('Add New Entry');
        parent::_construct();

    }
}
