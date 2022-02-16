<?php
namespace CustomConcepts\UltimoGocustomized\Block;

class ListFeaturedSlider extends \Infortis\Base\Block\Product\ProductList\Featured
{
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context, 
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Magento\Customer\Model\Session $modelSession,
        \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection,
        \Magento\Catalog\Helper\Output $catalogHelperOutput,
        \Magento\Catalog\Model\Layer\CategoryFactory $categoryLayerFactory,
        \Infortis\Base\Helper\Data $baseDataHelper,
        \Infortis\Base\Helper\Labels $baseLabelHelper,
        \Infortis\Infortis\Helper\Image $infortisImageHelper,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \CustomConcepts\UltimoGocustomized\Helper\Data $helper,
        array $data = array()
    ) {
        $this->helper = $helper;
        $this->categoryFactory = $categoryFactory;
        parent::__construct($context, $postDataHelper, $layerResolver, $categoryRepository, $urlHelper, $modelSession, $productCollection, $catalogHelperOutput, $categoryLayerFactory, $baseDataHelper, $baseLabelHelper, $infortisImageHelper, $categoryFactory, $data);
    }
    public function getParentCategory() {
        return $this->helper->getConfig(\CustomConcepts\UltimoGocustomized\Helper\Data::XML_PATH_ROOT_CATEGORY);
    }
    public function getCategory($categoryID) {
        return $this->categoryFactory->create()->load($categoryID);
    }
}