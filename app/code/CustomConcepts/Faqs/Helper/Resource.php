<?php
namespace CustomConcepts\Faqs\Helper;

use Magento\Framework\App\Helper\Context;

class Resource extends \Magento\Framework\App\Helper\AbstractHelper {
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resourceConnection;

    /**
     * Resource constructor.
     * @param Context $context
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     */
    public function __construct(
        Context $context,
        \Magento\Framework\App\ResourceConnection $resourceConnection
    ){
        $this->resourceConnection = $resourceConnection;
        parent::__construct($context);
    }

    /**
     * @param $storeId
     * @return array
     */
    public function getStoreTopicIds($storeId){
        $prodfaqsStore = $this->resourceConnection->getTableName('prodfaqs_store');

        $select = $this->resourceConnection->getConnection()->select()
            ->from($prodfaqsStore, ['topic_id'])
            ->where('store_id in (?)', [0, $storeId]);
        $rowSet = $this->resourceConnection->getConnection()->fetchAll($select);

        $topicIds = [];
        foreach ($rowSet as $row){
            $topicIds[] = $row['topic_id'];
        }

        return $topicIds;
    }

    /**
     * @param $topicId
     * @return array
     */
    public function getTopicStores($topicId){
        $prodfaqsStore = $this->resourceConnection->getTableName('prodfaqs_store');

        $select = $this->resourceConnection->getConnection()->select()
            ->from($prodfaqsStore, ['store_id'])
            ->where('topic_id = ?', $topicId);
        $rowSet = $this->resourceConnection->getConnection()->fetchAll($select);

        $storeIds = [];
        foreach ($rowSet as $row){
            $storeIds[] = $row['store_id'];
        }

        return $storeIds;
    }
}




