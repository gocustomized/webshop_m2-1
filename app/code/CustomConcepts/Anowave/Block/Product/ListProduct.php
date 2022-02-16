<?php
namespace CustomConcepts\Anowave\Block\Product;


use Magento\Catalog\Model\Layer;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\ScopeInterface;

class ListProduct extends \Magento\Catalog\Block\Product\ListProduct
{
    protected function _getProductCollection()
    {
        if ($this->_productCollection === null) {
            $this->_productCollection = $this->initializeProductCollection();

            /** filter collection by URL to fix Anowave issue */
            $storeId = $this->_storeManager->getStore()->getId();
            $order = $this->getRequest()->getParam('product_list_order') ?: $this->_scopeConfig->getValue('catalog/frontend/default_sort_by', ScopeInterface::SCOPE_STORE, $storeId);
            $dir = $this->getRequest()->getParam('product_list_dir') ?: 'asc';
            $limit = $this->getRequest()->getParam('product_list_limit') ?: $this->_scopeConfig->getValue('catalog/frontend/grid_per_page', ScopeInterface::SCOPE_STORE, $storeId);
            $currentPage = $this->getRequest()->getParam('p') ?: 1;

            $this->_productCollection->setOrder($order, $dir);
            $this->_productCollection->setCurPage($currentPage);
            if($limit != 'all'){
                $this->_productCollection->setPageSize($limit);
            }
        }

        return $this->_productCollection;
    }

    private function initializeProductCollection()
    {
        $layer = $this->getLayer();
        /* @var $layer Layer */
        if ($this->getShowRootCategory()) {
            $this->setCategoryId($this->_storeManager->getStore()->getRootCategoryId());
        }

        // if this is a product view page
        if ($this->_coreRegistry->registry('product')) {
            // get collection of categories this product is associated with
            $categories = $this->_coreRegistry->registry('product')
                ->getCategoryCollection()->setPage(1, 1)
                ->load();
            // if the product is associated with any category
            if ($categories->count()) {
                // show products from this category
                $this->setCategoryId(current($categories->getIterator())->getId());
            }
        }

        $origCategory = null;
        if ($this->getCategoryId()) {
            try {
                $category = $this->categoryRepository->get($this->getCategoryId());
            } catch (NoSuchEntityException $e) {
                $category = null;
            }

            if ($category) {
                $origCategory = $layer->getCurrentCategory();
                $layer->setCurrentCategory($category);
            }
        }
        $collection = $layer->getProductCollection();

        $this->prepareSortableFieldsByCategory($layer->getCurrentCategory());

        if ($origCategory) {
            $layer->setCurrentCategory($origCategory);
        }

        $this->_eventManager->dispatch(
            'catalog_block_product_list_collection',
            ['collection' => $collection]
        );

        return $collection;
    }
}
