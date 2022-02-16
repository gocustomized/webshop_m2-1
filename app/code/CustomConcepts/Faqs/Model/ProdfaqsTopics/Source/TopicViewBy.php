<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace CustomConcepts\Faqs\Model\ProdfaqsTopics\Source;

use Magento\Framework\Data\OptionSourceInterface;

class TopicViewBy implements OptionSourceInterface
{
    /**
     * @return array|array[]
     */
    public function toOptionArray()
    {
        return [
            ['label' => __('Title'), 'value' => 'title'],
            ['label' => __('Image'), 'value' => 'image']
        ];
    }
}
