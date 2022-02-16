<?php

namespace CustomConcepts\Estimations\Model;

use CustomConcepts\Estimations\Api\Data\EstimationDatesInterfaceFactory;
use CustomConcepts\Estimations\Api\Data\EstimationDatesSearchResultsInterfaceFactory;
use CustomConcepts\Estimations\Api\EstimationDatesRepositoryInterface;
use CustomConcepts\Estimations\Model\ResourceModel\EstimationDates as ResourceEstimationDates;
use CustomConcepts\Estimations\Model\ResourceModel\EstimationDates\CollectionFactory as EstimationDatesCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class EstimationDatesRepository
 *
 * @package CustomConcepts\Estimations\Model
 */
class EstimationDatesRepository implements EstimationDatesRepositoryInterface
{
    protected $searchResultsFactory;

    protected $dataObjectHelper;

    protected $dataObjectProcessor;

    protected $estimationDatesCollectionFactory;

    protected $extensionAttributesJoinProcessor;

    protected $dataEstimationDatesFactory;

    private $collectionProcessor;

    protected $estimationDatesFactory;

    protected $resource;

    private $storeManager;

    protected $extensibleDataObjectConverter;

    /**
     * @param ResourceEstimationDates $resource
     * @param EstimationDatesFactory $estimationDatesFactory
     * @param EstimationDatesInterfaceFactory $dataEstimationDatesFactory
     * @param EstimationDatesCollectionFactory $estimationDatesCollectionFactory
     * @param EstimationDatesSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceEstimationDates $resource,
        EstimationDatesFactory $estimationDatesFactory,
        EstimationDatesInterfaceFactory $dataEstimationDatesFactory,
        EstimationDatesCollectionFactory $estimationDatesCollectionFactory,
        EstimationDatesSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    )
    {
        $this->resource = $resource;
        $this->estimationDatesFactory = $estimationDatesFactory;
        $this->estimationDatesCollectionFactory = $estimationDatesCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataEstimationDatesFactory = $dataEstimationDatesFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->collectionProcessor = $collectionProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \CustomConcepts\Estimations\Api\Data\EstimationDatesInterface $estimationDates
    )
    {
        /* if (empty($estimationDates->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $estimationDates->setStoreId($storeId);
        } */

        $estimationDatesData = $this->extensibleDataObjectConverter->toNestedArray(
            $estimationDates,
            [],
            \CustomConcepts\Estimations\Api\Data\EstimationDatesInterface::class
        );

        $estimationDatesModel = $this->estimationDatesFactory->create()->setData($estimationDatesData);

        try {
            $this->resource->save($estimationDatesModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the estimationDates: %1',
                $exception->getMessage()
            ));
        }
        return $estimationDatesModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function get($estimationDatesId)
    {
        $estimationDates = $this->estimationDatesFactory->create();
        $this->resource->load($estimationDates, $estimationDatesId);
        if (!$estimationDates->getId()) {
            throw new NoSuchEntityException(__('EstimationDates with id "%1" does not exist.', $estimationDatesId));
        }
        return $estimationDates->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    )
    {
        $collection = $this->estimationDatesCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \CustomConcepts\Estimations\Api\Data\EstimationDatesInterface::class
        );

        $this->collectionProcessor->process($criteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $items = [];
        foreach ($collection as $model) {
            $items[] = $model->getDataModel();
        }

        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        \CustomConcepts\Estimations\Api\Data\EstimationDatesInterface $estimationDates
    )
    {
        try {
            $estimationDatesModel = $this->estimationDatesFactory->create();
            $this->resource->load($estimationDatesModel, $estimationDates->getEstimationdatesId());
            $this->resource->delete($estimationDatesModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the EstimationDates: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($estimationDatesId)
    {
        return $this->delete($this->get($estimationDatesId));
    }

    public function getLatest($order_id)
    {
        $estimation_collection = $this->estimationDatesCollectionFactory->create()
            ->addFieldToSelect('*')
            ->addFieldToFilter('order_id', $order_id)
//            ->setLimit(1)
            ->setOrder(
                'created_at',
                'desc'
            );

        return $estimation_collection->getFirstItem();
    }
}
