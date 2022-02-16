<?php
declare(strict_types=1);

namespace CustomConcepts\Wics\Service;

use CustomConcepts\Wics\Api\Data\WicsItemResponseInterface;
use CustomConcepts\Wics\Logger\Logger;
use CustomConcepts\Wics\Model\SlackAdapter;
use Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\DataObject;
use Magento\Inventory\Model\SourceItemRepository;
use Magento\InventoryApi\Api\Data\SourceItemInterface;
use Magento\InventoryApi\Api\SourceItemsSaveInterface;

class WicsItemUpdater
{
    const SAFETY_STOCK_QTY = 10;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var ProductResource
     */
    private $productResource;

    /**
     * @var CollectionFactory
     */
    private $productCollectionFactory;

    /**
     * @var SourceItemRepository
     */
    private $sourceItemRepository;

    /**
     * @var SourceItemsSaveInterface
     */
    private $sourceItemsSaveManager;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var SlackAdapter
     */
    private $slackAdapter;

    /**
     * contains all items that will be updated sku => qty
     * @var array
     */
    private $itemsToUpdate = [];

    /**
     * @param Logger $logger
     * @param ProductResource $productResource
     * @param CollectionFactory $productCollectionFactory
     * @param SourceItemRepository $sourceItemRepository
     * @param SourceItemsSaveInterface $sourceItemsSaveManager
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SlackAdapter $slackAdapter
     */
    public function __construct(
        Logger $logger,
        ProductResource $productResource,
        CollectionFactory $productCollectionFactory,
        SourceItemRepository $sourceItemRepository,
        SourceItemsSaveInterface $sourceItemsSaveManager,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SlackAdapter $slackAdapter
    ) {
        $this->logger = $logger;
        $this->productResource = $productResource;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->sourceItemRepository = $sourceItemRepository;
        $this->sourceItemsSaveManager = $sourceItemsSaveManager;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->slackAdapter = $slackAdapter;
    }

    /**
     * @param WicsItemResponseInterface $wicsItems
     * @throw \Exception
     */
    public function update(WicsItemResponseInterface $wicsItems) : void
    {
        $productCollection = $this->productCollectionFactory->create();
        $productCollection->addAttributeToSelect(['sku', 'name', 'date_on_stock'])
            ->joinField(
                'qty',
                'inventory_source_item',
                'quantity',
                'sku=sku',
                '{{table}}.source_code=\'default\'',
                'left'
            );

        $productCollection->addAttributeToFilter('sku', ['in' => $wicsItems->getSkuPool()]);
        $productCollection->addAttributeToFilter('stock_item_sku', ['null' => true]);

        foreach ($productCollection as $product) {
            $sku = $product->getSku();
            $originalQty = (int)$product->getData('qty');
            $newQty       = $wicsItems->getQtyBySku($sku);
            $this->itemsToUpdate[$sku] = $newQty;

            if ($safetyStockAttr = $product->getCustomAttribute('safety_stock')) {
                $safetyStock = $safetyStockAttr->getValue();
            } else {
                $safetyStock = self::SAFETY_STOCK_QTY;
            }

            $this->logger->notice(
                "Product: {$sku} stock_qty from: {$originalQty} to: {$newQty} safety_stock: {$safetyStock}"
                . ($originalQty!=$newQty ? ' DIFFERENCE' : '')
            );

            //Check if item has become oos. if the new qty is below safety && if the original/old is above.
            if ($newQty < $safetyStock && $originalQty >= $safetyStock) {
                $this->logger->notice("OOS Product: {$sku}");
                $this->updateOutStock($product);
                $this->fetchRelationsAndUpdateStock($product, $newQty, true);
            }

            //Check if item has become in stock. if the new qty is higher then safety and original was below.
            elseif ($newQty >= $safetyStock && $originalQty < $safetyStock) {
                $this->logger->notice("INSTOCK Product: {$sku}");
                $this->updateInStock($product);
                $this->fetchRelationsAndUpdateStock($product, $newQty, false);
            }
        }

        $this->updateStockQty();
    }

    // @todo remove this method, this can be executed by one query
    public function fetchRelationsAndUpdateStock(DataObject $product, $newQty, $out_stock)
    {
        // @todo add to the main query
        $productCollection = $this->productCollectionFactory->create()
            ->addAttributeToSelect(['sku', 'name', 'date_on_stock'])
            ->addAttributeToFilter('stock_item_sku', $product->getSku());

        foreach ($productCollection as $relatedProduct) {
            if ($out_stock) {
                $this->updateOutStock($relatedProduct);
            } else {
                $this->updateInStock($relatedProduct);
            }
            $this->itemsToUpdate[$relatedProduct->getSku()] = $newQty;
        }
    }

    /**
     * @param DataObject $product
     * @throws \Exception
     */
    public function updateOutStock(DataObject $product)
    {
        $this->logger->notice("Start updating oos stock product - {$product->getSku()}");
        $oosDate = $product->getDateOnStock() ?: null;

        if (($oosDate && strtotime($oosDate) <= time()) || empty($oosDate)) {
            $over_2_weeks = date('Y-m-d', strtotime('+2 weeks'));
            $product->setData('date_on_stock', $over_2_weeks);
            $this->productResource->saveAttribute($product, 'date_on_stock');
            $this->slackAdapter->send(
                "There is a NEW OOS {$product->getSku()} - {$product->getName()},
                 the Back on stock date has been automatically set to {$over_2_weeks}
                  <!channel> Please adjust manually if necessary."
            );
        } else {
            $this->slackAdapter->send(
                "Current OOS: {$product->getSku()} - {$product->getName()},
                date was already been manually set to: {$oosDate}"
            );
        }
    }

    /**
     * @param DataObject $product
     * @throws \Exception
     */
    public function updateInStock(DataObject $product)
    {
        $this->logger->notice("Start updating in stock product - {$product->getSku()}");
        $oosDate = $product->getDateOnStock() ?: null;

        if ($oosDate) {
            $product->unsetData('date_on_stock');
            $this->productResource->saveAttribute($product, 'date_on_stock');
            $this->slackAdapter->send(
                "BACK IN stock: {$product->getSku()} - {$product->getName()}
                <!channel> Please check if there are Backorders that can be manually dropped.");
        } else {
            $this->slackAdapter->send(
                "BACK IN stock: {$product->getSku()}  - {$product->getName()}
                 this product was already manually put back in stock.  <!channel>
                 Please check if there are Backorders that can be manually dropped");
        }
    }

    /**
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Validation\ValidationException
     */
    public function updateStockQty()
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(SourceItemInterface::SKU, array_keys($this->itemsToUpdate), 'in')
            ->create();

        $sourceItems =  $this->sourceItemRepository->getList($searchCriteria)->getItems();

        foreach ($sourceItems as $sourceItem) {
            if (key_exists($sourceItem->getSku(), $this->itemsToUpdate)) {
                $sourceItem->setQuantity($this->itemsToUpdate[$sourceItem->getSku()]);
                $sourceItem->setStatus(1);
            }
        }

        $this->sourceItemsSaveManager->execute($sourceItems);
    }
}
