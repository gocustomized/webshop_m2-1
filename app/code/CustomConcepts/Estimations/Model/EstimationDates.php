<?php


namespace CustomConcepts\Estimations\Model;

use Magento\Framework\Api\DataObjectHelper;
use CustomConcepts\Estimations\Api\Data\EstimationDatesInterfaceFactory;
use CustomConcepts\Estimations\Api\Data\EstimationDatesInterface;

/**
 * Class EstimationDates
 *
 * @package CustomConcepts\Estimations\Model
 */
class EstimationDates extends \Magento\Framework\Model\AbstractModel
{

    protected $estimationdatesDataFactory;

    protected $dataObjectHelper;

    protected $_eventPrefix = 'estimations_estimationdates';

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param EstimationDatesInterfaceFactory $estimationdatesDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \CustomConcepts\Estimations\Model\ResourceModel\EstimationDates $resource
     * @param \CustomConcepts\Estimations\Model\ResourceModel\EstimationDates\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        EstimationDatesInterfaceFactory $estimationdatesDataFactory,
        DataObjectHelper $dataObjectHelper,
        \CustomConcepts\Estimations\Model\ResourceModel\EstimationDates $resource,
        \CustomConcepts\Estimations\Model\ResourceModel\EstimationDates\Collection $resourceCollection,
        array $data = []
    ) {
        $this->estimationdatesDataFactory = $estimationdatesDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve estimationdates model with estimationdates data
     * @return EstimationDatesInterface
     */
    public function getDataModel()
    {
        $estimationdatesData = $this->getData();
        
        $estimationdatesDataObject = $this->estimationdatesDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $estimationdatesDataObject,
            $estimationdatesData,
            EstimationDatesInterface::class
        );
        
        return $estimationdatesDataObject;
    }
}

