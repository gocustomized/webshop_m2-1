<?php
namespace CustomConcepts\Faqs\Controller\adminhtml\Faqs;

use Magento\Backend\App\Action;

class Delete extends \CustomConcepts\Faqs\Controller\adminhtml\Manage
{
    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('faqs_id');

        if ($id) {
            try {
                $model = $this->prodfaqsModelFactory->create();
                $this->prodfaqsResource->load($model, $id);
                $this->prodfaqsResource->delete($model);
                $this->messageManager->addSuccessMessage(__('You deleted the FAQ.'));

                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['faqs_id' => $id]);
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find a FAQ to delete.'));
        return $resultRedirect->setPath('*/*/');
    }
}
