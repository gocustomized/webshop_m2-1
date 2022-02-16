<?php
namespace CustomConcepts\BlockScheduler\Model;

class BlockScheduler
{
    protected $blockRepository;
    protected $searchCriteriaBuilderFactory;
    protected $timezone;
    protected $filterBuilder;
    protected $filterGroupBuilder;
    protected $cache;

    public function __construct(
        \Magento\Cms\Api\BlockRepositoryInterface $blockRepository,
        \Magento\Framework\Api\SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Magento\Framework\Api\Search\FilterGroupBuilder $filterGroupBuilder,
        \Magento\Framework\App\CacheInterface $cache
    ){
        $this->blockRepository = $blockRepository;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        $this->timezone = $timezone;
        $this->filterBuilder = $filterBuilder;
        $this->filterGroupBuilder = $filterGroupBuilder;
        $this->cache = $cache;
    }

    public function activateBlocks(){
        /** @var \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder */
        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
        $dateNow = $this->timezone->date()->format('Y-m-d H:i:s');

        /** OR filter */
        $dateEndFilter = $this->filterBuilder->setField('end_date')
                                             ->setValue($dateNow)
                                             ->setConditionType('gt')
                                             ->create();
        $dateEndFilterNull = $this->filterBuilder->setField('end_date')
                                             ->setConditionType('null')
                                             ->create();
        $filterOr = $this->filterGroupBuilder->addFilter($dateEndFilter)
                                             ->addFilter($dateEndFilterNull)
                                             ->create();
        $searchCriteriaBuilder->setFilterGroups([$filterOr]);

        /** Normal filters */
        $searchCriteriaBuilder->addFilter('is_active', 0)
                              ->addFilter('start_date', $dateNow, 'lteq');

        $searchCriteria = $searchCriteriaBuilder->create();
        $blocks = $this->blockRepository->getList($searchCriteria);

        $this->ItemsSetIsActive($blocks->getItems(), 1);
    }

    public function deactivateBlocks(){
        /** @var \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder */
        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
        $dateNow = $this->timezone->date()->format('Y-m-d H:i:s');

        $searchCriteria = $searchCriteriaBuilder->addFilter('is_active', 1)
            ->addFilter('end_date', $dateNow, 'lteq')
            ->create();
        $blocks = $this->blockRepository->getList($searchCriteria);

        $this->ItemsSetIsActive($blocks->getItems(), 0);
    }

    protected function ItemsSetIsActive($items, $isActive = 1){
        foreach ($items as $item){
            $item->setIsActive($isActive);
            $block = $this->blockRepository->save($item);
            $block->cleanModelCache();
            $this->cache->clean('cms_b_' . $block->getId());
        }
    }

}
