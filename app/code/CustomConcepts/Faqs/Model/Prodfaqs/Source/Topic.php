<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace CustomConcepts\Faqs\Model\Prodfaqs\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Topic implements OptionSourceInterface
{
    /**
     * @var \CustomConcepts\Faqs\Model\ResourceModel\ProdfaqsTopics\CollectionFactory
     */
    protected $prodfaqsTopicsCollectionFactory;

    /**
     * Topic constructor.
     * @param \CustomConcepts\Faqs\Model\ResourceModel\ProdfaqsTopics\CollectionFactory $prodfaqsTopicsCollectionFactory
     */
    public function __construct(
        \CustomConcepts\Faqs\Model\ResourceModel\ProdfaqsTopics\CollectionFactory $prodfaqsTopicsCollectionFactory
    ){
        $this->prodfaqsTopicsCollectionFactory = $prodfaqsTopicsCollectionFactory;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $collection = $this->prodfaqsTopicsCollectionFactory->create()->addFieldToSelect(['topic_id', 'title']);

        $data = [];
        foreach ($collection as $topic){
            $data[] = [
                'label' => $topic->getTitle(),
                'value' => $topic->getId()
            ];
        }
        return $data;
    }
}
