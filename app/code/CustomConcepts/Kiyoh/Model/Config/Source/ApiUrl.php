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
class ApiUrl implements ArrayInterface {

    /**
     * @return array
     */
    public function toOptionArray() {
        return [
            'www.kiyoh.nl' => __('Kiyoh.nl'),
            'www.kiyoh.com' => __('Kihoy.com'),
        ];
    }

}
