<?php
namespace CustomConcepts\Wics\Model;

use CustomConcepts\Wics\Model\ResourceModel\Sync\Collection;
use CustomConcepts\Wics\Model\ResourceModel\Sync\CollectionFactory;
use CustomConcepts\Wics\Model\SyncFactory;

class SyncRepository
{
    /**
     * @var SyncFactory
     */
    private $syncFactory;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var ResourceModel\Sync
     */
    private $resourceModel;

    /**
     * @param SyncFactory $syncFactory
     * @param CollectionFactory $collectionFactory
     * @param ResourceModel\Sync $resourceModel
     */
    public function __construct(
        SyncFactory $syncFactory,
        CollectionFactory $collectionFactory,
        ResourceModel\Sync $resourceModel
    ) {
        $this->syncFactory = $syncFactory;
        $this->collectionFactory = $collectionFactory;
        $this->resourceModel = $resourceModel;
    }

    /**
     * @return \CustomConcepts\Wics\Model\Sync[]
     */
    public function getList()
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();

        return $collection->getItems();
    }

    /**
     * @param Sync $syncModel
     */
    public function save(Sync $syncModel)
    {
        $this->resourceModel->save($syncModel);
    }

    /**
     * @param Sync $syncModel
     */
    public function delete(Sync $syncModel)
    {
        $this->resourceModel->delete($syncModel);
    }
}
