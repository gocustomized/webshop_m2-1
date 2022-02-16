<?php
namespace CustomConcepts\Faqs\Controller\adminhtml\Manage;

use Magento\Backend\App\Action;

class Delete extends \CustomConcepts\Faqs\Controller\adminhtml\Manage
{
    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('topic_id');

        if ($id) {
            try {
                $model = $this->prodfaqsTopicModelFactory->create();
                $this->prodfaqsTopicResource->load($model, $id);
                $this->uploaderHelper->deleteImage($model->getImage());
                $this->prodfaqsTopicResource->delete($model);
                $this->messageManager->addSuccessMessage(__('You deleted the Topic.'));

                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['topic_id' => $id]);
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find a Topic to delete.'));
        return $resultRedirect->setPath('*/*/');
    }
}
