<?php
namespace CustomConcepts\CheckoutFieldsConfig\Swissup\FieldManager\Controller\Adminhtml\Index;


class Edit extends \Swissup\FieldManager\Controller\Adminhtml\Index\Edit
{
    protected $storeManager;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry, \Magento\Eav\Model\EntityFactory $eavEntityFactory,
        \Magento\Customer\Model\AttributeFactory $customerAttributeFactory,
        \Magento\Store\Model\WebsiteFactory $websiteFactory,
        \Magento\Catalog\Model\Product\UrlFactory $urlFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Eav\Model\Config $eavModelConfig,
        \Magento\Eav\Model\Entity\Attribute\SetFactory $eavAttributeSetFactory,
        \Swissup\FieldManager\Helper\Data $helper,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ){
        $this->storeManager = $storeManager;
        parent::__construct($context, $resultPageFactory, $registry, $eavEntityFactory, $customerAttributeFactory, $websiteFactory, $urlFactory, $resultJsonFactory, $eavModelConfig, $eavAttributeSetFactory, $helper);
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('attribute_id');
        $model = $this->initModel()->setEntityTypeId($this->entityTypeId);

        /** load by store ID */
        if($storeId = $this->getRequest()->getParam('store')){
            $store = $this->storeManager->getStore($storeId);
            $model->setWebsite($store->getWebsiteId());
            $model->setData('store_id', $storeId);
        }

        if ($id) {
            $model->load($id);

            if (!$model->getId()) {
                $this->messageManager->addError(__('This attribute no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }

            // entity type check
            if ($model->getEntityTypeId() != $this->entityTypeId) {
                $this->messageManager->addError(__('This attribute cannot be edited.'));
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        }

        // set entered data if was error when we do save
        $data = $this->_session->getAttributeData(true);
        if (!empty($data)) {
            $model->addData($data);
        }
        $attributeData = $this->getRequest()->getParam('attribute');
        if (!empty($attributeData) && $id === null) {
            $model->addData($attributeData);
        }

        $this->coreRegistry->register('entity_attribute', $model);

        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu(static::ACTIVE_MENU)
            ->addBreadcrumb(__(static::TITLE), __(static::TITLE))
            ->addBreadcrumb(
                $id ? __(static::EDIT_TITLE) : __(static::NEW_TITLE),
                $id ? __(static::EDIT_TITLE) : __(static::NEW_TITLE)
            );
        $resultPage->getConfig()->getTitle()->prepend(__(static::TITLE));
        $resultPage->getConfig()->getTitle()->prepend($id ? $model->getFrontendLabel() : __(static::NEW_TITLE));

        return $resultPage;
    }
}
