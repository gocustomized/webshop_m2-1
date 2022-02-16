<?php

/**
 * CustomConcepts_Kiyoh extension
 * @category  CustomConcepts_Extensions
 * @package   CustomConcepts_Kiyoh
 * @copyright Copyright (c) 2017
 * @author GoCustomized <info@gocustomized.com>
 */

namespace CustomConcepts\Kiyoh\Block;

class Reviews extends \CustomConcepts\Kiyoh\Block\AbstractBlock {

    /**
     *
     * @var type \CustomConcepts\Kiyoh\Model\ResourceModel\Kiyohreview\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * 
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \CustomConcepts\Kiyoh\Model\Stats $stats
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \CustomConcepts\Kiyoh\Model\ResourceModel\Kiyohreview\CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(\Magento\Framework\View\Element\Template\Context $context, \CustomConcepts\Kiyoh\Model\Stats $stats, \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone, \CustomConcepts\Kiyoh\Model\ResourceModel\Kiyohreview\CollectionFactory $collectionFactory, array $data = array()) {
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context, $stats, $timezone, $data);

        $collection = $this->collectionFactory->create();
        $collection->setOrder('date_created', 'DESC');
        $collection->addFieldToFilter('status', 1);
        $collection->addFieldToFilter('score', array('gteq' => '7'));
        $collection->addFieldToFilter('shop_id', $this->getConfigValue('kiyoh/general/api_id'));
        $this->setReviews($collection);

        // Load Stats
        $_stats = $this->stats->load($this->getConfigValue('kiyoh/general/api_id'), 'shop_id');
        $this->setStats($_stats);
    }

    public function _prepareLayout() {
        parent::_prepareLayout();

        $pager = $this->getLayout()->createBlock('Magento\Theme\Block\Html\Pager', 'kiyoh.pager')->setTemplate('CustomConcepts_Kiyoh::html/pager.phtml');
        if ($this->getConfigValue('kiyoh/overview/enable_paging')) {
            $fieldPerPage = $this->getConfigValue('kiyoh/overview/paging_settings');
            $fieldPerPage = explode(',', $fieldPerPage);
            $fieldPerPage = array_combine($fieldPerPage, $fieldPerPage);
            $pager->setAvailableLimit($fieldPerPage);
        } else {
            $pager->setAvailableLimit(array('all' => 'all'));
        }
        $pager->setCollection($this->getReviews());
        $this->setChild('pager', $pager);
        $this->getReviews()->load();
        return $this;
    }

    /**
     * 
     * @return pager html
     */
    public function getPagerHtml() {
        return $this->getChildHtml('pager');
    }

}
