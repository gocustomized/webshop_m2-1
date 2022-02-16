<?php

/**
 * CustomConcepts_Kiyoh extension
 * @category  CustomConcepts_Extensions
 * @package   CustomConcepts_Kiyoh
 * @copyright Copyright (c) 2017
 * @author GoCustomized <info@gocustomized.com>
 */

namespace CustomConcepts\Kiyoh\Ui\Component\Listing\Column\Recommendation;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Options
 */
class Options implements OptionSourceInterface {

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray() {

        return [
            ['value' => '1', 'label' => 'Yes'],
            ['value' => '0', 'label' => 'No']
        ];
    }

}
