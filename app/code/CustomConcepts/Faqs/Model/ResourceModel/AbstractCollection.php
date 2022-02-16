<?php
namespace CustomConcepts\Faqs\Model\ResourceModel;

abstract class AbstractCollection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var \CustomConcepts\Faqs\Helper\Resource
     */
    protected $resourceHelper;
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * AbstractCollection constructor.
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \CustomConcepts\Faqs\Helper\Resource $resourceHelper
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\DB\Adapter\AdapterInterface|null $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb|null $resource
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \CustomConcepts\Faqs\Helper\Resource $resourceHelper,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ){
        $this->resourceHelper = $resourceHelper;
        $this->request = $request;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     * @param $storeId
     * @return $this
     */
    public function addStoreFilter($storeId){
        $topicIds = $this->resourceHelper->getStoreTopicIds($storeId);
        $this->getSelect()->where('main_table.topic_id in (?)', $topicIds);
        return $this;
    }

//    public function _renderFiltersBefore()
//    {
//        $filters = $this->request->getParam('filters');
//
//        parent::_renderFiltersBefore();
//    }

    protected function joinStoreRelationTable($tableName, $linkField)
    {
        $this->getSelect()->join(
            ['store_table' => $this->getTable($tableName)],
            'main_table.' . $linkField . ' = store_table.' . $linkField,
            ['store_id']
        )->group(
            'main_table.' . $linkField
        );
    }
}
