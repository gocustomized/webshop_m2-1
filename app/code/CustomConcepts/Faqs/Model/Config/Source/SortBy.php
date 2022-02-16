<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace CustomConcepts\Faqs\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class SortBy implements OptionSourceInterface
{
    /**
     * @return array|array[]
     */
    public function toOptionArray()
    {
        return [
            ['label' => __('Later'), 'value' => 'latest'],
            ['label' => __('Helpfull Rating'), 'value' => 'helpful'],
            ['label' => __('Order'), 'value' => 'order']
        ];
    }
}
