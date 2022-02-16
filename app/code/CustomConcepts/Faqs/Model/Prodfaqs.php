<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace CustomConcepts\Faqs\Model;

class Prodfaqs extends \Magento\Framework\Model\AbstractModel
{
    protected function _construct()
    {
        $this->_init(\CustomConcepts\Faqs\Model\ResourceModel\Prodfaqs::class);
    }

    public function getId()
    {
        return $this->getFaqsId();
    }

    public function setId($value)
    {
        return $this->setFaqsId($value);
    }
}
