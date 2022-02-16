<?php
namespace CustomConcepts\ConfigurablePrice\Plugin\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\ConfigurableProduct\Ui\DataProvider\Product\Form\Modifier\ConfigurablePanel;
use Magento\ConfigurableProduct\Ui\DataProvider\Product\Form\Modifier\ConfigurablePrice as ConfigurablePriceCore;

class ConfigurablePrice extends ConfigurablePriceCore
{
    public function afterModifyMeta($subject, $result)
    {
        $groupCode = $this->getGroupCodeByField($result, ProductAttributeInterface::CODE_PRICE)
            ?: $this->getGroupCodeByField($result, ConfigurablePriceCore::CODE_GROUP_PRICE);

        if (!empty($result[$groupCode]['children'][ConfigurablePriceCore::CODE_GROUP_PRICE])) {
            $result[$groupCode]['children'][ConfigurablePriceCore::CODE_GROUP_PRICE]['children']['price']['arguments']['data']['config']['component'] = 'Magento_Ui/js/form/element/abstract';
            $result[$groupCode]['children'][ConfigurablePriceCore::CODE_GROUP_PRICE]['children']['advanced_pricing_button']['arguments']['data']['config']['visible'] = 1;
            $result[$groupCode]['children'][ConfigurablePriceCore::CODE_GROUP_PRICE]['children']['advanced_pricing_button']['arguments']['data']['config']['disabled'] = 0;
        }

        return $result;
    }
}
