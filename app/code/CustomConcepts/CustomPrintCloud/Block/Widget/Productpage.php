<?php
namespace CustomConcepts\CustomPrintCloud\Block\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

class Productpage extends Template implements BlockInterface
{
    protected $_template = "widgets/productpage.phtml";
}
