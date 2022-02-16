<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace CustomConcepts\Base\Block\FrontendModel;

class Button extends \Magento\Config\Block\System\Config\Form\Field {

    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element) {
        $html = '';
        $this->setElement($element);

        $url = $this->getUrl('basemodule/importstoredata/index');

        $html = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')
                ->setType('button')
                ->setClass('scalable')
                ->setLabel('Import data')
                ->setOnClick("setLocation('$url')")
                ->toHtml();

        return $html;
    }

}
