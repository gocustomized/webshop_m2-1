<?php
namespace CustomConcepts\Faqs\Controller\adminhtml\Faqs;

use Magento\Backend\App\Action;

class Edit extends \CustomConcepts\Faqs\Controller\adminhtml\Manage
{
    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $result = $this->pageFactory->create();
        $result->setActiveMenu('CustomConcepts_Faqs::faqs')
            ->getConfig()->getTitle()->prepend(__('Manage Faqs'));
        return $result;
    }
}
