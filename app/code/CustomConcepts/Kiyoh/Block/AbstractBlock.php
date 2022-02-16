<?php

/**
 * CustomConcepts_Kiyoh extension
 * @category  CustomConcepts_Extensions
 * @package   CustomConcepts_Kiyoh
 * @copyright Copyright (c) 2017
 * @author GoCustomized <info@gocustomized.com>
 */

namespace CustomConcepts\Kiyoh\Block;

class AbstractBlock extends \Magento\Framework\View\Element\Template {

    /**
     *
     * @var type \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Scope config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     *
     * @var \CustomConcepts\Kiyoh\Model\Stats
     */
    protected $stats;

    /**
     *
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface 
     */
    protected $timezone;

    /**
     * 
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \CustomConcepts\Kiyoh\Model\Stats $stats
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param array $data
     */
    public function __construct(\Magento\Framework\View\Element\Template\Context $context, \CustomConcepts\Kiyoh\Model\Stats $stats, \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone, array $data = array()) {
        $this->scopeConfig = $context->getScopeConfig();
        $this->storeManager = $context->getStoreManager();
        $this->stats = $stats;
        $this->timezone = $timezone;
        parent::__construct($context, $data);
    }

    public function _prepareLayout() {
        return parent::_prepareLayout();
    }

    /**
     * 
     * @return review and rating score
     */
    public function getTotalScore() {
        $qty = 0;
        $rating = 0;
        $shop_id = $this->getConfigValue('kiyoh/general/api_id');
        $review_stats = $this->stats->load($shop_id, 'shop_id');
        if ($review_stats->getScore() > 0) {
            $review_stats->setPercentage($review_stats->getScore());
            $review_stats->setStarsQty(number_format(($review_stats->getScore() / 10), 1, ',', ''));
            return $review_stats;
        } else {
            return false;
        }
    }

    /**
     * 
     * @return link
     */
    function getExternalLink() {
        if ($this->getConfigValue('kiyoh/general/url')) {
            return __('on') . ' <a href="' . $this->getConfigValue('kiyoh/general/url') . '" target="_blank">KiyOh</a>';
        } else {
            return false;
        }
    }

    /**
     * 
     * @param type $rating
     * @return html for stars
     */
    function getHtmlStars($rating) {
        $html = '<div class="rating-box">';
        $html .= '	<div class="rating" style="width:' . $rating . '%"></div>';
        $html .= '</div>';
        return $html;
    }

    /**
     * Return Store code
     * @return type
     */
    public function getStoreCode() {
        return $this->storeManager->getStore()->getCode();
    }

    /**
     * Get configuration value
     * @param string $path
     * @return type mixed
     */
    public function getConfigValue($path) {
        return $this->scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * 
     * @param type $date
     * @param type $format
     * @return formatted date in format supplied
     */
    public function getFormattedDate($date, $format = null) {
        if ($format)
            return $this->timezone->date($date)->format($format);
        else
            return $this->timezone->date($date)->format(\Magento\Framework\Stdlib\DateTime::DATE_PHP_FORMAT);
    }

}
