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
class ImageHelper extends AbstractHelper
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
     * Check image
     *
     * @param image path $image
     * @return string
     */
    public function checkImage($image)
    {
        $path = $image->getImageUrl();
        $pattern = '!(.*)(cache\/)(.*?[\/])(.*)!i';
        $replacement = '$1$4';
        $new = preg_replace($pattern, $replacement, $path);

        $file_headers = @get_headers($new);    
        if (stripos($file_headers[0],"404 Not Found")>0 || (stripos($file_headers[0], "302 Found") > 0 && stripos($file_headers[7],"404 Not Found") > 0)) {
                return $path;
        }
        return $new;
    }
}
