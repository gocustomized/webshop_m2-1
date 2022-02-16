<?php
namespace CustomConcepts\RelatedProducts\Controller\Adminhtml\RelatedProducts;

class Index extends \Magento\Backend\App\Action
{
    protected $pageFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory
    ){
        $this->pageFactory = $pageFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $result = $this->pageFactory->create();
        $result->setActiveMenu('Magento_Catalog::catalog')
            ->getConfig()->getTitle()->prepend(__('Product Relationships'));

        return $result;
    }
}
