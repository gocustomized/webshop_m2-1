<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace CustomConcepts\Instagram\Block\Form;
class Login extends \Magento\Customer\Block\Form\Login
{
    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        return \Magento\Framework\View\Element\Template::_prepareLayout();
    }
}