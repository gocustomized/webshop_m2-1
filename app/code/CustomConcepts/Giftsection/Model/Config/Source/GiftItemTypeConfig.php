<?php
namespace CustomConcepts\Giftsection\Model\Config\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

class GiftItemTypeConfig extends AbstractSource
{
    protected $optionFactory;
    
    /**
     * Retrieve option array
     *
     * @return array
     */
    public static function getOptionArray()
    {
        return [
            'phone' => __('Phone'),
            'tablet' => __('Tablet'),
            'gadget' => __('Gadget'),
        ];
    }

    /**
     * Retrieve all options
     *
     * @return array
     */
    public function getAllOptions()
    {
        $this->_options = [];
        foreach (self::getOptionArray() as $index => $value) {
            $this->_options[] = ['value' => $index, 'label' => $value];
        }
        return $this->_options;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return $this->getAllOptions();
    }
}
