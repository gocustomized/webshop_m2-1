<?php

/**
 * CustomConcepts_Kiyoh extension
 * @category  CustomConcepts_Extensions
 * @package   CustomConcepts_Kiyoh
 * @copyright Copyright (c) 2017
 * @author GoCustomized <info@gocustomized.com>
 */

namespace CustomConcepts\Kiyoh\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Cronfrequency
 * @package CustomConcepts\Kiyoh\Model\Config\Source
 */
class Cronfrequency implements ArrayInterface {

    /**
     * @return array
     */
    public function toOptionArray() {
        return [
            '*/15 * * * *'  => __('Every 15 minutes'),
            '0 * * * *'     => __('Every Hour'),
            '0 */2 * * *'   => __('Every other Hour'),
            '0 8,20 * * *'  => __('Twice a Day'),
            '0 2 * * *'     => __('Once a Day'),
            '0 2 0 * *'     => __('Once a Week'),
        ];
    }

}
