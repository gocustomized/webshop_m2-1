<?php

/**
 * Integration edit container.
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace CustomConcepts\CustomAPI\Plugin\Block\Adminhtml\Integration\Edit\Tab;

class Info {

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
    \Magento\Framework\Registry $registry
    ) {
        $this->_coreRegistry = $registry;
    }

    /**
     * Get form HTML
     *
     * @return string
     */
    public function aroundGetFormHtml(
    \Magento\Integration\Block\Adminhtml\Integration\Edit\Tab\Info $subject, \Closure $proceed
    ) {
        $integrationData = $this->_coreRegistry->registry(\Magento\Integration\Controller\Adminhtml\Integration::REGISTRY_KEY_CURRENT_INTEGRATION);
        $form = $subject->getForm();
        if (is_object($form)) {

            //get form fieldset to add customer_id field 
            $fieldset = $form->getElement('base_fieldset');
            $fieldset->addField(
                    'customer_id', 'text', [
                'name' => 'customer_id',
                'label' => __('Customer Id'),
                'id' => 'customer_id',
                'title' => __('Customer Id'),
                'required' => false
                    ]
            );
            $form->setValues($integrationData);
            $subject->setForm($form);
        }

        return $proceed();
    }

}
