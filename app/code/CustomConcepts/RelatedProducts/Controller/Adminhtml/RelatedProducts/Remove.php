<?php
namespace CustomConcepts\RelatedProducts\Controller\Adminhtml\RelatedProducts;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;

class Remove extends \Magento\Backend\App\Action
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
        $linkType = $this->getRequest()->getParam('link_type');
        $algorithm = $this->getRequest()->getParam('algorithm');
        $selectedProducts = $this->getSelectedProducts();
        $rowsUpdated = 0;
        $errors = [];

        switch ($algorithm){
            case '0': //Remove relations between products only
                $selectedProductsSku = [];
                foreach ($selectedProducts as $selectedProduct){
                    $selectedProductsSku[] = $selectedProduct->getSku();
                }


                foreach ($selectedProducts as $selectedProduct){
                    $productLinks = $selectedProduct->getProductLinks();
                    foreach ($productLinks as $productLink){
                        if ($productLink->getLinkType() == $linkType && in_array($productLink->getLinkedProductSku(), $selectedProductsSku)){
                            try {
                                $this->productLinkRepository->deleteById($selectedProduct->getSku(), $linkType, $productLink->getLinkedProductSku());
                                $rowsUpdated += 1;
                            } catch (\Exception $e) {
                                $errors[] = __('Failed to process product id: ' . $selectedProduct->getId());
                            }
                        }
                    }
                }
                break;
            case '1': //Remove selected products from ALL relations in the catalog
                $linkTypes = $this->linkTypeProvider->getLinkTypes();
                $linkId = $linkTypes[$linkType];
                $connection = $this->resourceConnection->getConnection();
                $tableName = $connection->getTableName('catalog_product_link');

                foreach ($selectedProducts as $selectedProduct){
                    try {
                        $whereConditions = [
                            $connection->quoteInto('link_type_id = ?', $linkId),
                            $connection->quoteInto('linked_product_id = ?', $selectedProduct->getId()),
                        ];
                        $rows = $connection->delete($tableName, $whereConditions);
                        $rowsUpdated += $rows;
                    } catch (\Exception $e) {
                        $errors[] = __('Failed to process product id: ' . $selectedProduct->getId());
                    }
                }
                break;
            case '2': //Remove all relations from selected products
                /** @var \Magento\Catalog\Api\Data\ProductInterface $selectedProduct */
                foreach ($selectedProducts as $selectedProduct){
                    $productLinks = $selectedProduct->getProductLinks();
                    foreach ($productLinks as $productLink){
                        if ($productLink->getLinkType() == $linkType){
                            try {
                                $this->productLinkRepository->deleteById($selectedProduct->getSku(), $linkType, $productLink->getLinkedProductSku());
                                $rowsUpdated += 1;
                            } catch (\Exception $e) {
                                $errors[] = __('Failed to process product id: ' . $selectedProduct->getId());
                            }
                        }
                    }
                }
                break;
        }
        $this->messageManager->addSuccess(__('A total of %1 record(s) have been modified.', $rowsUpdated));
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
            $id = $this->getRequest()->getParam('selected'.$ctr);
            $selectedProducts[] = $this->productRepository->getById($id);
            $ctr +=1;
        }
        return $selectedProducts;
    }
}
