<?php
namespace CustomConcepts\HelpCenter\Controller\ContactForm;

use Magento\Framework\App\Action\Context;

class Index extends \Magento\Framework\App\Action\Action {

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
        return $this->pageFactory->create();
    }
}
