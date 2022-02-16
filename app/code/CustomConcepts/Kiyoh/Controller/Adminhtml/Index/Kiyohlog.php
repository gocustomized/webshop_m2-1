<?php

/**
 * CustomConcepts_Kiyoh extension
 * @category  CustomConcepts_Extensions
 * @package   CustomConcepts_Kiyoh
 * @copyright Copyright (c) 2017
 * @author GoCustomized <info@gocustomized.com>
 */

namespace CustomConcepts\Kiyoh\Controller\Adminhtml\Index;

use Magento\Framework\Controller\ResultFactory;

class Kiyohlog extends \CustomConcepts\Kiyoh\Controller\Adminhtml\AbstractAction {

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute() {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Manage KiyOh Logs'));
        return $resultPage;
    }

}
