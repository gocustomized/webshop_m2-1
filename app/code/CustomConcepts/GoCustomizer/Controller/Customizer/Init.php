<?php

/**
 * CustomConcepts_GoCustomizer extension
 * @category  CustomConcepts_Extensions
 * @package   CustomConcepts_GoCustomizer
 * @copyright Copyright (c) 2017
 * @author GoCustomized <info@gocustomized.com>
 */

namespace CustomConcepts\GoCustomizer\Controller\Customizer;

use Magento\Customer\Model\Session;
use Magento\Customer\Model\SessionFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Session\Generic;

class Init extends Action
{

    protected $pageFactory;

    public function __construct(
        Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory
    ) {
        $this->pageFactory = $pageFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        /** @var \Magento\Framework\View\Result\Page $page */
        $page = $this->pageFactory->create();
        $page->addHandle('gocustomizer_customizer_init');

        /** @var \Magento\Framework\View\Element\AbstractBlock $block */
        $block = $page->getLayout()->getBlock('gocustomizer');

        /** @var \Magento\Framework\Controller\Result\Raw $result */
        $result = $this->resultFactory->create($this->resultFactory::TYPE_RAW);
        $result->setContents($block ? $block->toHtml() : '');

        return $result;
    }
}
