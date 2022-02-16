<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace CustomConcepts\Base\Model\Config\Source;

class Options {

    public function toOptionArray() {
        return array(
            array('value' => 'all', 'label' => __('Copy All Data')),
            array('value' => 'configs', 'label' => __('Copy Store Configurations only')),
            array('value' => 'cms_blocks', 'label' => __('Copy CMS Blocks only')),
            array('value' => 'cms_pages', 'label' => __('Copy CMS Pages only')),
            array('value' => 'categories', 'label' => __('Copy Categories only')),
            array('value' => 'attributes', 'label' => __('Copy Product Attributes only')),
            array('value' => 'products', 'label' => __('Copy Products only')),
        );
    }

}
