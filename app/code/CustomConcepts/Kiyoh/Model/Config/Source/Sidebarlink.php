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
class Sidebarlink implements ArrayInterface {

    /**
     * @return array
     */
    public function toOptionArray() {
        return [
            ''         => __('None'),
            'external' => __('External (Kiyoh.nl)'),
            'internal' => __('Internal (/kiyoh)'),
        ];
    }

}
