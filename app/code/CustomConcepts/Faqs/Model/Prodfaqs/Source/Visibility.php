<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace CustomConcepts\Faqs\Model\Prodfaqs\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Visibility implements OptionSourceInterface
{
    /**
     * @return array|array[]
     */
    public function toOptionArray()
    {
        return [
            ['label' => __('Public'), 'value' => 'public'],
            ['label' => __('Private'), 'value' => 'private']
        ];
    }
}
