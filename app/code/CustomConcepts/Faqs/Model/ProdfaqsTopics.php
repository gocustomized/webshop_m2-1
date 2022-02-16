<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace CustomConcepts\Faqs\Model;

class ProdfaqsTopics extends \Magento\Framework\Model\AbstractModel
{
    protected function _construct()
    {
        $this->_init(\CustomConcepts\Faqs\Model\ResourceModel\ProdfaqsTopics::class);
    }

    public function getId()
    {
        return $this->getTopicId();
    }

    public function setId($value)
    {
        return $this->setTopicId($value);
    }
}
