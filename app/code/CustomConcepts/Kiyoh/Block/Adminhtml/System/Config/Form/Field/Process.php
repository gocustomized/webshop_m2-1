<?php

/**
 * CustomConcepts_Kiyoh extension
 * @category  CustomConcepts_Extensions
 * @package   CustomConcepts_Kiyoh
 * @copyright Copyright (c) 2017
 * @author GoCustomized <info@gocustomized.com>
 */

namespace CustomConcepts\Kiyoh\Block\Adminhtml\System\Config\Form\Field;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Process extends Field {

    /**
     * @var string
     */
    protected $_template = 'CustomConcepts_Kiyoh::system/config/process.phtml';

    /**
     * @param Context $context
     * @param array $data
     */
    public function __construct(
    Context $context, array $data = []
    ) {

        parent::__construct($context, $data);
    }

    /**
     * Return element html
     *
     * @param  AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element) {
        return $this->_toHtml();
    }

    /**
     * Generate collect button html
     *
     * @return string
     */
    public function getButtonHtml() {
        $button = $this->getLayout()->createBlock(
                        'Magento\Backend\Block\Widget\Button'
                )->setData(
                [
                    'id' => 'fetch_button',
                    'label' => __('Process Manually'),
                    'onclick' => "setLocation('" . $this->getUrl('kiyohreview/process') . "')",
                ]
        );

        return $button->toHtml();
    }

}
