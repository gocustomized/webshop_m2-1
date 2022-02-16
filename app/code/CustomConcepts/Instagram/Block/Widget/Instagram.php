<?php
/**
 * CustomConcepts Infotech
 * CustomConcepts Instagram Extension
 * 
 * @category   CustomConcepts
 * @package    CustomConcepts_Instagram
 * @copyright  Copyright © 2006-2016 CustomConcepts
 *  
 */
namespace CustomConcepts\Instagram\Block\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

class Instagram extends Template  implements BlockInterface
{
    
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->setTemplate('widget/instagram.phtml');
    }
    

}