<?php
/**
 * CustomConcepts_ReorderDiscountCode extension
 * @category  CustomConcepts_Extensions
 * @package   CustomConcepts_ReorderDiscountCode
 * @copyright Copyright (c) 2017
 * @author GoCustomized <info@gocustomized.com>
 */

namespace CustomConcepts\ReorderDiscountCode\Model\Config\Source;

class SimpleAction implements \Magento\Framework\Option\ArrayInterface {

    public function toOptionArray() {
        return [
            ['value' => 'by_percent', 'label' => __('Percent of product price discount')],
            ['value' => 'by_fixed', 'label' => __('Fixed amount discount')],
            ['value' => 'cart_fixed', 'label' => __('Fixed amount discount for whole cart')]
        ];
    }

}
