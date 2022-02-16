<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace CustomConcepts\Faqs\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class DisplayTopics implements OptionSourceInterface
{
    /**
     * @return array|array[]
     */
    public function toOptionArray()
    {
        return [
            ['label' => __('All'), 'value' => 'all'],
            ['label' => __('Selected'), 'value' => 'selected']
        ];
    }
}
