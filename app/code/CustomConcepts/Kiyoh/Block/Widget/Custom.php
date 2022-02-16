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

class Custom extends \CustomConcepts\Kiyoh\Block\AbstractBlock implements BlockInterface {

    /**
     *
     * @var type string
     */
    protected $_template = "CustomConcepts_Kiyoh::widget/custom.phtml";

    /**
     * 
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \CustomConcepts\Kiyoh\Model\Stats $stats
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param array $data
     */
    public function __construct(\Magento\Framework\View\Element\Template\Context $context, \CustomConcepts\Kiyoh\Model\Stats $stats, \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone, array $data = array()) {
        parent::__construct($context, $stats, $timezone, $data);
        $this->addData(array(
            'cache_lifetime' => 7200,
            'cache_tags' => array(\Magento\Cms\Model\Block::CACHE_TAG, \CustomConcepts\Kiyoh\Model\Kiyohreview::CACHE_TAG),
            'cache_key' => $this->_storeManager->getStore()->getStoreId() . '-kiyoh-custom-block',
        ));

        if ($this->getConfigValue('kiyoh/general/enabled')) {
            $this->setTemplate('CustomConcepts_Kiyoh::widget/custom.phtml');
        }
    }

    /**
     * 
     * @return total score html
     */
    public function getKiyohData() {
        return $this->getTotalScore();
    }

}
