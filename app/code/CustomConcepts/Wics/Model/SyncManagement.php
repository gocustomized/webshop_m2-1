<?php
namespace CustomConcepts\Wics\Model;

use CustomConcepts\Wics\Model\ResourceModel\Sync\CollectionFactory;
use Magento\Framework\Data\Collection;

class SyncManagement
{
    const MAXIMUM_SYNC_RECORDS = 100;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var SyncRepository
     */
    private $repository;

    /**
     * @param CollectionFactory $collectionFactory
     * @param SyncRepository $repository
     */
    public function __construct(CollectionFactory $collectionFactory, SyncRepository $repository)
    {
        $this->collectionFactory = $collectionFactory;
        $this->repository = $repository;
    }

    /**
     * @return Sync | bool
     */
    public function getLatestSuccessful()
    {
        /** @var ResourceModel\Sync\Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('status', 'successful');
        $collection->addOrder('creation_time', Collection::SORT_ORDER_DESC);
        $collection->setPageSize(1);

        return count($collection) ? $collection->getFirstItem() : false;
    }

    /**
     * Clean old sync records
     * @param int|null $numberOfRecords
     */
    public function clean(int $numberOfRecords = null)
    {
        /** @var ResourceModel\Sync\Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection->addOrder('creation_time', Collection::SORT_ORDER_ASC);
        $collection->setPageSize($numberOfRecords ? $numberOfRecords : static::MAXIMUM_SYNC_RECORDS);
        $itemsToRemove = $collection->getItems();

        foreach ($itemsToRemove as $item) {
            $this->repository->delete($item);
        }
    }
}
