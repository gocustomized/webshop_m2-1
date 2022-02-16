<?php

/**
 * CustomConcepts_Kiyoh extension
 * @category  CustomConcepts_Extensions
 * @package   CustomConcepts_Kiyoh
 * @copyright Copyright (c) 2017
 * @author GoCustomized <info@gocustomized.com>
 */

namespace CustomConcepts\Kiyoh\Controller\Adminhtml\Process;

/**
 * Process manual for crons
 */
class Index extends \CustomConcepts\Kiyoh\Controller\Adminhtml\AbstractAction {

    /**
     *
     * @var \CustomConcepts\Kiyoh\Model\Api $_api
     */
    protected $_api;

    /**
     *
     * @var \Magento\Framework\Message\ManagerInterface 
     */
    protected $messageManager;

    /**
     *
     * @var \CustomConcepts\Kiyoh\Model\Kiyohlog
     */
    protected $kiyohLog;

    /**
     *
     * @var \CustomConcepts\Kiyoh\Model\Stats
     */
    protected $stats;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \CustomConcepts\Kiyoh\Model\AbstractKiyohModel $abstractModel
     * @param \CustomConcepts\Kiyoh\Model\Stats $stats
     * @param \CustomConcepts\Kiyoh\Model\Api $_api
     * @param \CustomConcepts\Kiyoh\Model\Kiyohlog $kiyohLog
     */
    public function __construct(\Magento\Backend\App\Action\Context $context, \CustomConcepts\Kiyoh\Model\AbstractKiyohModel $abstractModel, \CustomConcepts\Kiyoh\Model\Stats $stats, \CustomConcepts\Kiyoh\Model\Api $_api, \CustomConcepts\Kiyoh\Model\Kiyohlog $kiyohLog) {
        parent::__construct($context, $abstractModel);
        $this->_Api = $_api;
        $this->stats = $stats;
        $this->kiyohLog = $kiyohLog;
        $this->messageManager = $context->getMessageManager();
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute() {
        $storeids = $this->_Api->getStoreIds();
        $start_time = microtime(true);

        foreach ($storeids as $storeid) {
            $msg = '';
            $api_id = $this->getConfigValue('kiyoh/general/api_id', $storeid);
            $result = $this->_Api->processFeed($storeid, 'history');
            $log = $this->kiyohLog->addToLog('reviews', $storeid, $result, '', (microtime(true) - $start_time), '', '');

            if (($result['review_new'] > 0) || ($result['review_updates'] > 0) || ($result['stats'] == true)) {
                $msg = __(sprintf('Webwinkel ID %s:', $api_id)) . ' ';
                $msg .= __(sprintf('%s new review(s)', $result['review_new'])) . ', ';
                $msg .= __(sprintf('%s review(s) updated', $result['review_updates'])) . ' & ';
                $msg .= __(sprintf('and total score updated.'));
            }

            if ($msg) {
                $this->messageManager->addSuccess($msg);
            } else {
                $this->messageManager->addError(__(sprintf('Company ID %s: no updates found, feed is empty or not found!', $api_id)));
            }
        }
        $this->stats->processOverall();
        $this->_redirect('adminhtml/system_config/edit/section/kiyoh');
    }

}
