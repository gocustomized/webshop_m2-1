<?php

/**
 * CustomConcepts_Kiyoh extension
 * @category  CustomConcepts_Extensions
 * @package   CustomConcepts_Kiyoh
 * @copyright Copyright (c) 2017
 * @author GoCustomized <info@gocustomized.com>
 */

namespace CustomConcepts\Kiyoh\Controller\Adminhtml\MassAction;

use Magento\Framework\Controller\ResultFactory;

class MassRemovesidebar extends \CustomConcepts\Kiyoh\Controller\Adminhtml\AbstractAction {

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

        $reviewIds = $this->getRequest()->getParam('selected');
        if (!is_array($reviewIds)) {
            $this->messageManager->addError(__('Please select item(s)'));
        } else {
            try {
                foreach ($reviewIds as $review_id) {
                    $reviews = $this->_kiyohreview->load($review_id);
                    $reviews->setSidebar(0)->save();
                }
                $this->messageManager->addSuccess(__(sprintf('Total of %d review(s) were removed from the sidebar.', count($reviewIds))));
            } catch (Exception $e) {
                $this->messageManager->addError(__($e->getMessage()));
            }
        }
        $this->_redirect('*/');
    }

}
