<?php

/**
 * CustomConcepts_ReorderDiscountCode extension
 * @category  CustomConcepts_Extensions
 * @package   CustomConcepts_ReorderDiscountCode
 * @copyright Copyright (c) 2017
 * @author GoCustomized <info@gocustomized.com>
 */

namespace CustomConcepts\ReorderDiscountCode\Plugin\Controller\Adminhtml\Promo\Quote;

class Save {

    /**
     *
     * @var \Magento\Backend\Model\Auth 
     */
    protected $auth;

    /**
     * 
     * @param \Magento\Backend\Model\Auth $auth
     */
    public function __construct(\Magento\Backend\Model\Auth $auth) {
        $this->auth = $auth;
    }

    /**
     * Add our custom column into post data before it save rule
     */
    public function beforeExecute(\Magento\SalesRule\Controller\Adminhtml\Promo\Quote\Save $subject) {
        //set our custom column into Post Data
        $subject->getRequest()->setPostValue('created_by', $this->auth->getUser()->getUsername());
    }

}
