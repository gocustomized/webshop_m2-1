<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace CustomConcepts\Base\Model\Config\Source;

class Position implements \Magento\Framework\Option\ArrayInterface {
    public function toOptionArray() {

        return [
            ['value' => 'top', 'label' => __('TOP')],
            ['value' => 'bottom', 'label' => __('BOTTOM')]
        ];
        
    }
}
?>
