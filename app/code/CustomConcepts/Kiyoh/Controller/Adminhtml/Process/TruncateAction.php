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

class TruncateAction extends \CustomConcepts\Kiyoh\Controller\Adminhtml\AbstractAction {

    /**
     *
     * @var \CustomConcepts\Kiyoh\Model\Kiyohreview
     */
    protected $_kiyohreview;

    /**
     *
     * @var \Magento\Framework\Message\ManagerInterface 
     */
    protected $messageManager;

    /**
     * 
     * @param \Magento\Backend\App\Action\Context $context
     * @param \CustomConcepts\Kiyoh\Model\AbstractKiyohModel $abstractModel
     * @param \CustomConcepts\Kiyoh\Model\Kiyohreview $_kiyohreview
     */
    public function __construct(\Magento\Backend\App\Action\Context $context, \CustomConcepts\Kiyoh\Model\AbstractKiyohModel $abstractModel, \CustomConcepts\Kiyoh\Model\Kiyohreview $_kiyohreview) {
        parent::__construct($context, $abstractModel);
        $this->_kiyohreview = $_kiyohreview;
        $this->messageManager = $context->getMessageManager();
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute() {
        $i = 0;
        $collection = $this->_kiyohreview->getCollection();
        try {
            foreach ($collection as $item) {
                $item->delete();
                $i++;
            }
            $this->messageManager->addSuccess(__(sprintf('Succefully deleted all %s saved review(s).', $i)));
        } catch (Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
        }

        $this->_redirect('*/');
    }

}
