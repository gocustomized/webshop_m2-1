<?php

/**
 * CustomConcepts_Kiyoh extension
 * @category  CustomConcepts_Extensions
 * @package   CustomConcepts_Kiyoh
 * @copyright Copyright (c) 2017
 * @author GoCustomized <info@gocustomized.com>
 */

namespace CustomConcepts\Kiyoh\Block;

class Sidebar extends \CustomConcepts\Kiyoh\Block\AbstractBlock {

    /**
     *
     * @var type \CustomConcepts\Kiyoh\Model\ResourceModel\Kiyohreview\CollectionFactory
     */
    protected $collectionFactory;

    /**
     *
     * @var type \Magento\Framework\Filter\FilterManager
     */
    protected $filter;

    /**
     * 
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \CustomConcepts\Kiyoh\Model\Stats $stats
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \CustomConcepts\Kiyoh\Model\ResourceModel\Kiyohreview\CollectionFactory $collectionFactory
     * @param \Magento\Framework\Filter\FilterManager $filter
     * @param array $data
     */
    public function __construct(\Magento\Framework\View\Element\Template\Context $context, \CustomConcepts\Kiyoh\Model\Stats $stats, \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone, \CustomConcepts\Kiyoh\Model\ResourceModel\Kiyohreview\CollectionFactory $collectionFactory, \Magento\Framework\Filter\FilterManager $filter, array $data = array()) {
        $this->collectionFactory = $collectionFactory;
        $this->filter = $filter;
        parent::__construct($context, $stats, $timezone, $data);
    }

    /**
     * Get the collection for sidebar reviews 
     * @param type $sidebar
     * @return collection
     */
    function getSidebarCollection($sidebar) {
        $enabled = '';
        $qty = '5';
        if ($this->getConfigValue('kiyoh/general/enabled')) {
            if ($sidebar == 'left') {
                $qty = $this->getConfigValue('kiyoh/sidebar/left_qty');
                $enabled = $this->getConfigValue('kiyoh/sidebar/left');
            }
            if ($sidebar == 'right') {
                $qty = $this->getConfigValue('kiyoh/sidebar/right_qty');
                $enabled = $this->getConfigValue('kiyoh/sidebar/right');
            }
        }

        if ($enabled) {
            $shop_id = $this->getConfigValue('kiyoh/general/api_id');
            $collection = $this->collectionFactory->create();
            $collection->setOrder('date_created', 'DESC');
            $collection->addFieldToFilter('status', 1);
            $collection->addFieldToFilter('sidebar', 1);
            $collection->addFieldToFilter('shop_id', array('eq' => array($shop_id)));
            $collection->setPageSize($qty);
            $collection->load();
            return $collection;
        } else {
            return false;
        }
    }

    /**
     * Format a content for sidebar display
     * @param type $sidebarreview
     * @param type $sidebar
     * @return formatted content
     */
    function formatContent($sidebarreview, $sidebar = 'left') {
        $content = $sidebarreview->getPositive();
        if ($sidebar == 'left') {
            $char_limit = $this->getConfigValue('kiyoh/sidebar/left_lenght');
        }
        if ($sidebar == 'right') {
            $char_limit = $this->getConfigValue('kiyoh/sidebar/right_lenght');
        }
        $content = $this->filter->truncate($content, ['length' => $char_limit, 'etc' => '...']);
        if ($content) {
            $content = '"' . $content . '"';
        }
        return $content;
    }

    /**
     * Return reviews with link
     * @param type $sidebar
     * @return string
     */
    function getReviewsUrl($sidebar = 'left') {
        $link = '';
        $url = '';
        if ($sidebar == 'left') {
            $link = $this->getConfigValue('kiyoh/sidebar/left_link');
        }
        if ($sidebar == 'right') {
            $link = $this->getConfigValue('kiyoh/sidebar/right_link');
        }
        if ($link == 'internal') {
            $url = $this->getUrl('kiyoh');
            $target = '';
        }
        if ($link == 'external') {
            $url = $this->getConfigValue('kiyoh/general/url');
            $target = 'target="_blank"';
        }
        if ($url) {
            return '<a href="' . $url . '" ' . $target . '>' . __('View all reviews') . '</a>';
        } else {
            return false;
        }
    }

    /**
     * Check wether sidebar is enabled or not
     * @param type $sidebar
     * @return boolean
     */
    function getSnippetsEnabled($sidebar = 'left') {
        $enabled = '';
        if ($sidebar == 'left'):
            $enabled = $this->getConfigValue('kiyoh/sidebar/left_snippets');
        endif;
        if ($sidebar == 'right'):
            $enabled = $this->getConfigValue('kiyoh/sidebar/right_snippets');
        endif;
        if ($enabled) {
            return true;
        } else {
            return false;
        }
    }

}
