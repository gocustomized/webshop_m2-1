<?php
namespace CustomConcepts\Faqs\Controller\adminhtml\Faqs;

use Magento\Backend\App\Action;

class Save extends \CustomConcepts\Faqs\Controller\adminhtml\Manage
{
    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        $model = $this->prodfaqsModelFactory->create();

        if($data){
            if(isset($data['faqs_id'])){
                $this->prodfaqsResource->load($model, $data['faqs_id']);
                if(!$model->getId()){
                    $this->messageManager->addErrorMessage(__('This FAQ no longer exists.'));
                    return $resultRedirect->setPath('*/*/');
                }
            }

            $model->setData($data);

            try {
                $this->prodfaqsResource->save($model);
                $this->messageManager->addSuccessMessage(__('You saved the FAQ.'));
                return $resultRedirect->setPath('*/*/edit', ['faqs_id' => $model->getId()]);
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the FAQ.'));
            }
        }

        return $resultRedirect->setPath('*/*/');
    }
}
