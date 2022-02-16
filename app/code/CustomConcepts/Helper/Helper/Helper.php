<?php

namespace CustomConcepts\Helper\Helper;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Category as ModelCategory;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\Store;

/**
 * Catalog category helper
 *
 */
class Helper extends AbstractHelper
{
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Category factory
     *
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $_categoryFactory;

    /**
     * Lib data collection factory
     *
     * @var \Magento\Framework\Data\CollectionFactory
     */
    protected $_dataCollectionFactory;

    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Data\CollectionFactory $dataCollectionFactory
     * @param CategoryRepositoryInterface $categoryRepository
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\CollectionFactory $dataCollectionFactory,
        CategoryRepositoryInterface $categoryRepository
    ) {
        $this->_categoryFactory = $categoryFactory;
        $this->_storeManager = $storeManager;
        $this->_dataCollectionFactory = $dataCollectionFactory;
        $this->categoryRepository = $categoryRepository;
        parent::__construct($context);
    }

    /**
     * Retrieve sub Category By Id
     *
     * @param ModelCategory $category
     * @return string
     */
    public function getHomeUrl($storeId)
    {
        return $this->_storeManager->getStore($storeId)->getBaseUrl();;
    }
}
