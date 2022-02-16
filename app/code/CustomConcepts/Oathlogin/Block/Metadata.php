<?php

/**
 * CustomConcepts_Oathlogin extension
 * @category  CustomConcepts_Extensions
 * @package   CustomConcepts_Oathlogin
 * @copyright Copyright (c) 2017
 * @author GoCustomized <info@gocustomized.com>
 */

namespace CustomConcepts\Oathlogin\Block;

use Magento\Framework\View\Result\PageFactory;

class Metadata extends \Magento\Backend\Block\Template{
    
    protected $pageFactory;


    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        PageFactory $pageFactory,
        array $data = array()
    ) {
        $this->pageFactory = $pageFactory;
        parent::__construct($context, $data);
    }
    
    
}