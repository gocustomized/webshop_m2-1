<?php
namespace CustomConcepts\Checkout\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class TrackingMethod implements OptionSourceInterface
{
    /**
     * @return array|array[]
     */
    public function toOptionArray()
    {
        return [
            ['label' => __('N/A'), 'value' => ''],
            ['label' => __('Not Tracked'), 'value' => 'not_tracked'],
            ['label' => __('Tracked with signature'), 'value' => 'tracked_with_sign'],
            ['label' => __('Tracked without signature'), 'value' => 'tracked_without_sign']
        ];
    }

    /**
     * @param $value
     * @return mixed|string
     */
    public function getLabel($value){
        $options = $this->toOptionArray();

        if($value){
            foreach ($options as $option){
                if($option['value'] == $value){
                    return $option['label'];
                }
            }
        }
        return '';
    }
}
