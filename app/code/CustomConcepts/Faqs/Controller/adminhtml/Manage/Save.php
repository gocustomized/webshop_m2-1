<?php
namespace CustomConcepts\Faqs\Controller\adminhtml\Manage;

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
        $model = $this->prodfaqsTopicModelFactory->create();

        if($data){
            if(isset($data['topic_id'])){
                $this->prodfaqsTopicResource->load($model, $data['topic_id']);
                if(!$model->getId()){
                    $this->messageManager->addErrorMessage(__('This topic no longer exists.'));
                    return $resultRedirect->setPath('*/*/');
                }
            }

            if(isset($data['image_upload'])){
                $image = $this->uploadImage($data);
                if($image){
                    $data['image'] = $image;
                    $this->uploaderHelper->deleteImage($model->getImage());
                }
                unset($data['image_upload']);
            }

            $model->setData($data);

            try {
                $this->prodfaqsTopicResource->save($model);
                $this->messageManager->addSuccessMessage(__('You saved the topic.'));
                return $resultRedirect->setPath('*/*/edit', ['topic_id' => $model->getId()]);
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the topic.'));
            }
        }

        return $resultRedirect->setPath('*/*/');
    }

    protected function uploadImage($data){
        if(isset($data['image_upload'][0]['size'])){ //check if a new file was uploaded
            $uploader = $this->uploaderHelper->getImageUploader();
            $move = $uploader->moveFileFromTmp($data['image_upload'][0]['name'], true);
            $this->uploaderHelper->clearTmpDir();

            return $this->uploaderHelper->getMediaPath() . $move;
        }
    }
}
