<?php
namespace CustomConcepts\Estimations\Block\Adminhtml\Order\View\Tab;


class EstimatonDates extends \Magento\Framework\View\Element\Text\ListText implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    protected $_coreRegistry = null;

    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $data);
    }

    public function getOrder()
    {
        return $this->_coreRegistry->registry('current_order');
    }

    public function getTabLabel()
    {
        return __('Estimation Dates');
    }

    public function getTabTitle()
    {
        return __('Estimation Dates');
    }

    public function canShowTab()
    {
        if ($this->getOrder()->getIsVirtual()) {
            return false;
        }
        return true;
    }

    public function isHidden()
    {
        return false;
    }

}
