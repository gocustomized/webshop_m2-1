<?php
namespace CustomConcepts\Faqs\Controller\Index;

use Magento\Framework\App\Action\Context;

class View extends \Magento\Framework\App\Action\Action {

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $pageFactory;

    /**
     * Index constructor.
     * @param Context $context
     * @param \Magento\Framework\View\Result\PageFactory $pageFactory
     */
    public function __construct(
        Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory
    )
    {
        parent::__construct($context);

        $this->pageFactory = $pageFactory;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $term = $this->getRequest()->getParam('faqsearch') ?: '';
        if($term){
            return $this->_redirect('*/*/search', array('term' => $term));
        }

        return $this->pageFactory->create();
    }
}
