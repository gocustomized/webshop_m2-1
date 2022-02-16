<?php
namespace CustomConcepts\GhostShipment\Controller\Adminhtml\Ship;


use Magento\Backend\App\Action\Context;

class index extends \Magento\Backend\App\Action
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
        return $this->pageFactory->create();
    }
}
