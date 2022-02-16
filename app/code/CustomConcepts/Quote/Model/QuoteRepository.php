<?php
/**
 * CustomConcepts_QuoteRepository extension
 * @category  CustomConcepts_Extensions
 * @package   CustomConcepts_QuoteRepository
 * @copyright Copyright (c) 2017
 * @author GoCustomized <info@gocustomized.com>
 */
namespace CustomConcepts\Quote\Model;

use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\Data\CartInterfaceFactory;
use Magento\Quote\Api\Data\CartSearchResultsInterfaceFactory;
use Magento\Quote\Model\QuoteFactory;
use Magento\Quote\Model\ResourceModel\Quote\Collection as QuoteCollection;
use Magento\Quote\Model\ResourceModel\Quote\CollectionFactory as QuoteCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Quote repository.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class QuoteRepository extends \Magento\Quote\Model\QuoteRepository
{
    /**
     * @var CartInterfaceFactory
     */
    private $cartFactory;

    public function __construct(QuoteFactory $quoteFactory, StoreManagerInterface $storeManager, QuoteCollection $quoteCollection, CartSearchResultsInterfaceFactory $searchResultsDataFactory, JoinProcessorInterface $extensionAttributesJoinProcessor, CollectionProcessorInterface $collectionProcessor = null, QuoteCollectionFactory $quoteCollectionFactory = null, CartInterfaceFactory $cartFactory = null)
    {
        $this->cartFactory = $cartFactory ?: ObjectManager::getInstance()->get(CartInterfaceFactory::class);
        parent::__construct($quoteFactory, $storeManager, $quoteCollection, $searchResultsDataFactory, $extensionAttributesJoinProcessor, $collectionProcessor, $quoteCollectionFactory, $cartFactory);
    }

    /**
     * @inheritdoc
     */
    public function getActive($cartId, array $sharedStoreIds = [])
    {
        $quote = $this->cartFactory->create();
        if ($cartId) {
            $quote = $this->get($cartId, $sharedStoreIds);
            if (!$quote->getIsActive()) {
                throw NoSuchEntityException::singleField('cartId', $cartId);
            }
        }
        return $quote;
    }

    /**
     * Load quote with different methods
     *
     * @param string $loadMethod
     * @param string $loadField
     * @param int $identifier
     * @param int[] $sharedStoreIds
     * @throws NoSuchEntityException
     * @return CartInterface
     */
    protected function loadQuote($loadMethod, $loadField, $identifier, array $sharedStoreIds = [])
    {
        /** @var CartInterface $quote */
        $quote = $this->cartFactory->create();
        if ($sharedStoreIds && is_callable([$quote, 'setSharedStoreIds'])) {
            $quote->setSharedStoreIds($sharedStoreIds);
        }
        $quote->setStoreId($this->storeManager->getStore()->getId())->$loadMethod($identifier);
        if (!$quote->getId()) {
            throw NoSuchEntityException::singleField($loadField, $identifier);
        }
        return $quote;
    }
}
