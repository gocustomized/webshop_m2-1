<?php
namespace CustomConcepts\RelatedProducts\Controller\Adminhtml\RelatedProducts;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;

class Assign extends \Magento\Backend\App\Action
{
    protected $productRepository;
    protected $productLinkRepository;
    protected $productLinkManagement;
    protected $productLinkInterfaceFactory;
    protected $messageManager;
    protected $resultFactory;
    protected $linkTypeProvider;
    protected $resourceConnection;

    public function __construct(
        Context $context,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Api\ProductLinkRepositoryInterface $productLinkRepository,
        \Magento\Catalog\Api\ProductLinkManagementInterface $productLinkManagement,
        \Magento\Catalog\Api\Data\ProductLinkInterfaceFactory $productLinkInterfaceFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Controller\ResultFactory $resultFactory,
        \Magento\Catalog\Model\Product\LinkTypeProvider $linkTypeProvider,
        \Magento\Framework\App\ResourceConnection $resourceConnection
    ){
        $this->productRepository = $productRepository;
        $this->productLinkRepository = $productLinkRepository;
        $this->productLinkManagement = $productLinkManagement;
        $this->productLinkInterfaceFactory = $productLinkInterfaceFactory;
        $this->messageManager = $messageManager;
        $this->resultFactory = $resultFactory;
        $this->linkTypeProvider = $linkTypeProvider;
        $this->resourceConnection = $resourceConnection;
        parent::__construct($context);
    }

    public function execute()
    {
        $linkTypes = $this->linkTypeProvider->getLinkTypes();
        $linkType = $this->getRequest()->getParam('link_type');
        $linkId = $linkTypes[$linkType];
        $ids = $this->getRequest()->getParam('ids');
        $idsArr = explode(',', $ids);
        $selectedProducts = $this->getSelectedProducts();
        $rowsUpdated = 0;
        $errors = [];

        foreach ($idsArr as $productId){
            $connection = $this->resourceConnection->getConnection();
            $tableName = $connection->getTableName('catalog_product_link');

            foreach ($selectedProducts as $selectedProduct){
                $data = [
                  'product_id' => $productId,
                  'linked_product_id' => $selectedProduct,
                  'link_type_id' => $linkId
                ];
                if ($connection->insertOnDuplicate($tableName, $data)){
                    $rowsUpdated += 1;
                }
            }
        }

        $this->messageManager->addSuccess(__('A total of %1 record(s) have been assigned.', $rowsUpdated));
        if($errors){
            foreach ($errors as $error){
                $this->messageManager->addError($error);
            }
        }
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('catalog/product/index');
    }

    private function getSelectedProducts(){
        $selectedProducts = [];
        $ctr = 1;
        while ($this->getRequest()->getParam('selected'.$ctr)){
            $selectedProducts[] = $this->getRequest()->getParam('selected'.$ctr);
            $ctr +=1;
        }
        return $selectedProducts;
    }

}
