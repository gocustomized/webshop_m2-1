<?php


namespace Razoyo\AnimalProfile\Model\Animals\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Animals implements OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            ['label' => __('Cat'), 'value' => 'cat'],
            ['label' => __('Dog'), 'value' => 'dog'],
            ['label' => __('Llama'), 'value' => 'llama'],
            ['label' => __('Anteater'), 'value' => 'anteater'],
        ];
    }
}
