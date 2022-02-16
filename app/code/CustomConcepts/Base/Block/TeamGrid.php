<?php


namespace CustomConcepts\Base\Block;


use Magento\Framework\View\Element\Template;

class TeamGrid extends Template
{
    public function __construct(Template\Context $context, array $data = [])
    {
        parent::__construct($context, $data);
        $this->setData('teamMembers', []);
    }
}
