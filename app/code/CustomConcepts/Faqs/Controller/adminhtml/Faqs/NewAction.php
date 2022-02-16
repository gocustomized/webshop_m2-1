<?php
namespace CustomConcepts\Faqs\Controller\adminhtml\Faqs;

use Magento\Backend\App\Action;

class NewAction extends \CustomConcepts\Faqs\Controller\adminhtml\Manage
{
    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultForward = $this->resultForwardFactory->create();
        return $resultForward->forward('edit');
    }
}
