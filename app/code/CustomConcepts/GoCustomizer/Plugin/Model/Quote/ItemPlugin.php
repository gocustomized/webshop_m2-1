<?php

/**
 * CustomConcepts_GoCustomizer extension
 * @category  CustomConcepts_Extensions
 * @package   CustomConcepts_GoCustomizer
 * @copyright Copyright (c) 2017
 * @author GoCustomized <info@gocustomized.com>
 */

namespace CustomConcepts\GoCustomizer\Plugin\Model\Quote;

class ItemPlugin {
    
    /**
     *
     * @var type \CustomConcepts\GoCustomizer\Helper\Data
     */
    protected  $customizerHelper;
    /**
     * 
     * @param \CustomConcepts\GoCustomizer\Helper\Data $customizerHelper
     */
    public function __construct(
        \CustomConcepts\GoCustomizer\Helper\Data $customizerHelper
    ) {
        $this->customizerHelper = $customizerHelper;
    }

    public function afterRepresentProduct(\Magento\Quote\Model\Quote\Item $subject, $result){
        if($this->customizerHelper->isGocustomizerProduct($subject)){
            $result = false;
        }
        return $result;
    }
}