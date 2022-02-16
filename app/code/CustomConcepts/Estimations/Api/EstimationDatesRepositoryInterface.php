<?php


namespace CustomConcepts\Estimations\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Interface EstimationDatesRepositoryInterface
 *
 * @package CustomConcepts\Estimations\Api
 */
interface EstimationDatesRepositoryInterface
{

    /**
     * Save EstimationDates
     * @param \CustomConcepts\Estimations\Api\Data\EstimationDatesInterface $estimationDates
     * @return \CustomConcepts\Estimations\Api\Data\EstimationDatesInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \CustomConcepts\Estimations\Api\Data\EstimationDatesInterface $estimationDates
    );

    /**
     * Retrieve EstimationDates
     * @param string $estimationdatesId
     * @return \CustomConcepts\Estimations\Api\Data\EstimationDatesInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($estimationdatesId);

    /**
     * Retrieve EstimationDates matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \CustomConcepts\Estimations\Api\Data\EstimationDatesSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete EstimationDates
     * @param \CustomConcepts\Estimations\Api\Data\EstimationDatesInterface $estimationDates
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \CustomConcepts\Estimations\Api\Data\EstimationDatesInterface $estimationDates
    );

    /**
     * Delete EstimationDates by ID
     * @param string $estimationdatesId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($estimationdatesId);
}

