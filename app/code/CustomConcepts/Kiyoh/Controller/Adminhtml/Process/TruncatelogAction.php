<?php

/**
 * CustomConcepts_Kiyoh extension
 * @category  CustomConcepts_Extensions
 * @package   CustomConcepts_Kiyoh
 * @copyright Copyright (c) 2017
 * @author GoCustomized <info@gocustomized.com>
 */

namespace CustomConcepts\Kiyoh\Controller\Adminhtml\Process;

use Magento\Framework\Controller\ResultFactory;

class TruncatelogAction extends \CustomConcepts\Kiyoh\Controller\Adminhtml\AbstractAction {

    /**
     * Scope config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     *
     * @var type \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     *
     * @var type \CustomConcepts\Kiyoh\Model\Kiyohlog
     */
    protected $_kiyohlog;

    public function __construct(\Magento\Backend\App\Action\Context $context, \CustomConcepts\Kiyoh\Model\AbstractKiyohModel $abstractModel, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Magento\Store\Model\StoreManagerInterface $storeManager, \CustomConcepts\Kiyoh\Model\Kiyohlog $_kiyohlog) {
        parent::__construct($context, $abstractModel);
        $this->messageManager = $context->getMessageManager();
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->_kiyohlog = $_kiyohlog;
    }

    /**
     * return config value
     * @param type $path
     * @param type $store
     * @return type
     */
    public function getConfigValue($path, $store = null) {
        if (!empty($store)):
            return $this->scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
        else:
            return $this->scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        endif;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute() {
        $enabled = $this->getConfigValue('kiyoh/log/clean', $this->storeManager->getStore());
        $days = $this->getConfigValue('kiyoh/log/clean_days', $this->storeManager->getStore());
        $i = 0;
        if (($enabled) && ($days > 0)) {
            $deldate = date('Y-m-d', strtotime('-' . $days . ' days'));
            $logs = $this->_kiyohlog->getCollection()->addFieldToSelect('id')->addFieldToFilter('date', array('lteq' => $deldate));
            foreach ($logs as $log) {
                $this->_kiyohlog->load($log->getId())->delete();
                $i++;
            }
            $this->messageManager->addSuccess(__(sprintf('Total of %s log record(s) deleted.', $i)));
        }

        $this->_redirect('*/');
    }

}
