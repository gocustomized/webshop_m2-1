<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace CustomConcepts\Faqs\Model\Prodfaqs\Source;

use Magento\Framework\Data\OptionSourceInterface;

class QuestionType implements OptionSourceInterface
{
    /**
     * @return array|array[]
     */
    public function toOptionArray()
    {
        return [
            ['label' => __('General Question'), 'value' => 'general_question'],
            ['label' => __('Product Question'), 'value' => 'product_question']
        ];
    }
}
