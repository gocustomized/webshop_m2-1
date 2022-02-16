<?php
namespace CustomConcepts\ProductionDashboard\Controller\Adminhtml\Index;


use Magento\Backend\App\Action\Context;

class Index extends \Magento\Backend\App\Action
{
    protected $pageFactory;

    public function __construct(
        Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory
    ){
        $this->pageFactory = $pageFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $result = $this->pageFactory->create();
        $result->setActiveMenu('CustomConcepts_ProductionDashboard::production_dashboards')
            ->getConfig()->getTitle()->prepend(__('Production Dashboard'));
        return $result;
    }

    public function _isAllowed()
    {
        return $this->_authorization->isAllowed('CustomConcepts_ProductionDashboard::production_dashboard');
    }
}
