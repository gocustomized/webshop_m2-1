<?php


namespace CustomConcepts\Estimations\Api\Data;

/**
 * Interface EstimationDatesSearchResultsInterface
 *
 * @package CustomConcepts\Estimations\Api\Data
 */
interface EstimationDatesSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get EstimationDates list.
     * @return \CustomConcepts\Estimations\Api\Data\EstimationDatesInterface[]
     */
    public function getItems();

    /**
     * Set order_id list.
     * @param \CustomConcepts\Estimations\Api\Data\EstimationDatesInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

