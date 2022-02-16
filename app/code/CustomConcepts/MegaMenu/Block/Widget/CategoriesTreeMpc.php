<?php
/**
 * Copyright © 2016 Codazon. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace CustomConcepts\MegaMenu\Block\Widget;

/**
 * Html page top menu block
 */
class CategoriesTreeMpc extends \Codazon\MegaMenu\Block\Widget\CategoriesTree
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;
    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $categoryFactory;
    /**
     * @var
     */
    public $productHtml;

    /**
     * CategoriesTree constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Data\Tree\NodeFactory $nodeFactory
     * @param \Magento\Framework\Data\TreeFactory $treeFactory
     * @param \Codazon\MegaMenu\Helper\Category $catalogCategory
     * @param \Magento\Catalog\Model\Indexer\Category\Flat\State $categoryFlatState
     * @param \Magento\Catalog\Observer\MenuCategoryData $menuCategoryData
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Data\Tree\NodeFactory $nodeFactory,
        \Magento\Framework\Data\TreeFactory $treeFactory,
        \Codazon\MegaMenu\Helper\Category $catalogCategory,
        \Magento\Catalog\Model\Indexer\Category\Flat\State $categoryFlatState,
        \Magento\Catalog\Observer\MenuCategoryData $menuCategoryData,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        array $data = []
    ) {
        parent::__construct($context, $nodeFactory, $treeFactory, $catalogCategory, $categoryFlatState, $menuCategoryData, $data);
        $this->productCollectionFactory = $productCollectionFactory;
        $this->categoryFactory = $categoryFactory;
    }

    /**
     * @param $categories
     * @param $parentCategoryNode
     * @param $block
     * @param int $level
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function _addCategoriesToMenu($categories, $parentCategoryNode, $block, $level = 1)
    {
        foreach ($categories as $category) {
            if($this->getData('category_filter')){
                $filters = explode(',', $this->getData('category_filter'));
                if(!in_array($category->getId(), $filters)){
                    continue;
                }
            }
            if (!$category->getIsActive()) {
                continue;
            }
            $block->addIdentity(\Magento\Catalog\Model\Category::CACHE_TAG . '_' . $category->getId());

            $tree = $parentCategoryNode->getTree();
            $categoryData = $this->menuCategoryData->getMenuCategoryData($category);
            $categoryNode = new \Magento\Framework\Data\Tree\Node($categoryData, 'id', $tree, $parentCategoryNode);
            $parentCategoryNode->addChild($categoryNode);

            if ($this->categoryFlatState->isFlatEnabled() && $category->getUseFlatResource()) {
                $subcategories = (array)$category->getChildrenNodes();
            } else {
                $subcategories = $category->getChildren();
            }

            //retrieve product for last categories
            $maxLevel = $this->getData('max_level') ?: 0;
            if((count($subcategories) == 0 || $level == $maxLevel) && $this->getData('include_products')){
                $this->setData('category-node-' . $category->getId(), $level);
                $this->_addProductsToMenu($category, $categoryNode, $block);
            } else {
                $level += 1;
                $this->_addCategoriesToMenu($subcategories, $categoryNode, $block, $level);
                $level  -= 1;
            }
        }
    }

    /**
     * @param $category
     * @param $parentCategoryNode
     * @param $block
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function _addProductsToMenu($category, $parentCategoryNode, $block)
    {
        $categoryModel = $this->categoryFactory->create()->load($category->getId());

        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        $collection->addCategoryFilter($categoryModel);
        $collection->addAttributeToFilter('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH);
        $collection->addAttributeToFilter('status',\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);

        foreach ($collection as $product){
            $block->addIdentity(\Magento\Catalog\Model\Category::CACHE_TAG . '_' . $category->getId() . '_product_' . $product->getId());

            $tree = $parentCategoryNode->getTree();

            $productData = [
                'name' => $product->getShortName(),
                'typedevice' => $product->getAttributeText('typedevice'),
                'id' => 'category-node-' . $category->getId() . '-product-' . $product->getId(),
                'url' => $product->getProductUrl(),
//                'image' => $image_url,
                'has_active' => false,
                'is_active' => true,
                'is_product' => true,
            ];

            if($this->getData('include_product_image') == 1){
                $image_url = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product' .$product->getImage();
                $productData['image'] = $image_url;
            }


            $categoryNode = new \Magento\Framework\Data\Tree\Node($productData, 'id', $tree, $parentCategoryNode);
            $parentCategoryNode->addChild($categoryNode);
        }
    }

    /**
     * @param \Magento\Framework\Data\Tree\Node $menuTree
     * @param string $childrenWrapClass
     * @param int $limit
     * @param array $colBrakes
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _getHtml(
        \Magento\Framework\Data\Tree\Node $menuTree,
        $childrenWrapClass,
        $limit,
        $colBrakes = []
    ) {
        $html = '';

        $itemCount = $this->getData('item_count');

        $children = $menuTree->getChildren();
        $parentLevel = $menuTree->getLevel();
        $childLevel = ($parentLevel === null) ? 1 : $parentLevel + 1;

        $counter = 1;
        $itemPosition = 1;
        $childrenCount = $children->count();

        $parentPositionClass = $menuTree->getPositionClass();
        $itemPositionClassPrefix = $parentPositionClass ? $parentPositionClass . '-' : 'nav-';

        foreach ($children as $child) {
            $child->setLevel($childLevel);
            $child->setIsFirst($counter == 1);
            $child->setIsLast($counter == $childrenCount);
            $child->setPositionClass($itemPositionClassPrefix . $counter);

            $outermostClassCode = '';
            $outermostClass = $menuTree->getOutermostClass();

            if ($childLevel == 0 && $outermostClass) {
                $outermostClassCode = ' class="' . $outermostClass . '" ';
                $child->setClass($outermostClass);
            }

            $addClass = [];
            if($this->_storeManager->getWebsite()->getCode() == 'custom_concepts'){
                if(!$child->getIsProduct()){
                    $addClass[] = 'mpc-link';
                } else {
                    $addClass[] = 'mpc-product-link';
                }
            }

            if($this->getData('include_product_image')){
//                $linkImage = $child->getImage() ? '<img class="item-category-img desktop-view" src="' . $child->getImage() . '">' : '';
                $linkImage = '<span class="loadimage" data-src="' . $child->getImage() . '"></span>';
            } else {
                $addClass[] = 'no-image-link';
                $linkImage = '<span class="device-type-span">' . $child->getTypedevice() . '</span>&nbsp;';
            }


            $html .= '<li ' . $this->_getRenderedMenuItemAttributes($child) . '>';
            $html .= '<a class="menu-link ' . implode(' ', $addClass) . '" node="' . $child->getId() . '" href="' . $child->getUrl() . '" ' . $outermostClassCode . '>' . $linkImage . '<span>' . $this->escapeHtml(
                    $child->getName()
                ) . '</span></a>' . $this->_addSubMenu(
                    $child,
                    $childLevel,
                    $childrenWrapClass,
                    $limit
                ) . '</li>';
            $itemPosition++;
            if($itemCount){
                if($itemCount == $counter){
                    break;
                }
            }

            $counter++;
        }

        return $html;
    }

    /**
     * @param \Magento\Framework\Data\Tree\Node $child
     * @param string $childLevel
     * @param string $childrenWrapClass
     * @param int $limit
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _addSubMenu($child, $childLevel, $childrenWrapClass, $limit)
    {
        $html = '';
        if (!$child->hasChildren()) {
            return $html;
        }

        $colStops = null;
        if ($childLevel == 0 && $limit) {
            $colStops = $this->_columnBrake($child->getChildren(), $limit);
        }

        $addClass = [];
        if($this->getData($child->getId()) == $childLevel){
            if($this->getData('include_product_image')){
                $addClass[] = 'product-list';
            } else {
                $addClass[] = 'product-list-no-image';
            }
        }

        if($this->_storeManager->getWebsite()->getCode() == 'custom_concepts' && $this->getData($child->getId()) == $childLevel){
            $node = $child->getId();
            $addClass[] = 'hidden-product-section';
            $addClass[] = $node;

            $this->productHtml .= '<ul class="' . implode(" ", $addClass) . '">';
            $this->productHtml .= $this->_getHtml($child, $childrenWrapClass, $limit, $colStops);
            $this->productHtml .= '</ul>';

            $addClass[] = 'mobile-view';
        }

        $html .= '<ul class="level' . $childLevel . ' groupmenu-drop ' . implode(" ", $addClass) . '">';
        $html .= $this->_getHtml($child, $childrenWrapClass, $limit, $colStops);
        $html .= '</ul>';

        return $html;
    }
}
