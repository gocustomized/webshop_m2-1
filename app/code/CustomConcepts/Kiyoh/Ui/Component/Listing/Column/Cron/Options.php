<?php

/**
 * CustomConcepts_Kiyoh extension
 * @category  CustomConcepts_Extensions
 * @package   CustomConcepts_Kiyoh
 * @copyright Copyright (c) 2017
 * @author GoCustomized <info@gocustomized.com>
 */

namespace CustomConcepts\Kiyoh\Ui\Component\Listing\Column\Cron;

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
            ['value' => '', 'label' => __('Manual')],
            ['value' => 'stats', 'label' => __('Stats Cron')],
            ['value' => 'reviews', 'label' => __('Reviews Cron')],
            ['value' => 'orderupdate', 'label' => __('Invitation')]
        ];
    }

}
