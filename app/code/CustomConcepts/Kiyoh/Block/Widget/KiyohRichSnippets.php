<?php

/**
 * CustomConcepts_Kiyoh extension
 * @category  CustomConcepts_Extensions
 * @package   CustomConcepts_Kiyoh
 * @copyright Copyright (c) 2017
 * @author GoCustomized <info@gocustomized.com>
 */

namespace CustomConcepts\Kiyoh\Block\Widget;

use Magento\Widget\Block\BlockInterface;

class KiyohRichSnippets extends \CustomConcepts\Kiyoh\Block\AbstractBlock implements BlockInterface {

    /**
     *
     * @var type string
     */
    protected $_template = "CustomConcepts_Kiyoh::widget/richsnippets.phtml";

    /**
     * 
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \CustomConcepts\Kiyoh\Model\Stats $stats
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param array $data
     */
    public function __construct(\Magento\Framework\View\Element\Template\Context $context, \CustomConcepts\Kiyoh\Model\Stats $stats, \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone, array $data = array()) {
        parent::__construct($context, $stats, $timezone, $data);
    }

    /**
     * 
     * @return total score html
     */
    public function getSnippets() {
        return $this->getTotalScore();
    }

}
