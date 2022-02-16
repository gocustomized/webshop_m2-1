<?php
declare(strict_types=1);

namespace CustomConcepts\GhostShipment\Plugin\UiComponent\DataProvider;

use CustomConcepts\GhostShipment\Api\GhostShipmentCreatorInterface;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\Search\SearchCriteriaInterface;
use Magento\Framework\Data\Collection;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\UiComponent\DataProvider\Reporting as SubjectReporting;

class Reporting
{
    /**
     * @var FilterGroupBuilder
     */
    private $filterGroupBuilder;

    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

    /**
     * @param FilterGroupBuilder $filterGroupBuilder
     * @param FilterBuilder $filterBuilder
     */
    public function __construct(FilterGroupBuilder $filterGroupBuilder, FilterBuilder $filterBuilder)
    {
        $this->filterGroupBuilder = $filterGroupBuilder;
        $this->filterBuilder = $filterBuilder;
    }

    /**
     * @param SubjectReporting $subject
     * @param Collection $result
     * @param SearchCriteriaInterface $searchCriteria
     * @return Collection
     * @throws LocalizedException
     */
    public function afterSearch(SubjectReporting $subject, Collection $result, SearchCriteriaInterface $searchCriteria)
    {
        if ($searchCriteria->getRequestName() === 'sales_order_view_shipment_grid_data_source') {
            $result->addFieldToFilter('ghost_shipment', ['neq' => 1]);
        }

        return $result;
    }
}
