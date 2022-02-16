<?php

/**
 * CustomConcepts_Kiyoh extension
 * @category  CustomConcepts_Extensions
 * @package   CustomConcepts_Kiyoh
 * @copyright Copyright (c) 2017
 * @author GoCustomized <info@gocustomized.com>
 */

namespace CustomConcepts\Kiyoh\Controller\Adminhtml;

use Magento\Framework\Controller\ResultFactory;

class AbstractAction extends \Magento\Backend\App\Action {

    /**
     *
     * @var type \CustomConcepts\Kiyoh\Model\AbstractKiyohModel
     */
    protected $abstractModel;

    /**
     * 
     * @param \Magento\Backend\App\Action\Context $context
     * @param \CustomConcepts\Kiyoh\Model\AbstractKiyohModel $abstractModel
     */
    public function __construct(\Magento\Backend\App\Action\Context $context, \CustomConcepts\Kiyoh\Model\AbstractKiyohModel $abstractModel
    ) {
        $this->abstractModel = $abstractModel;
        parent::__construct($context);
    }

    public function getConfigValue($path, $store = null) {
        return $this->abstractModel->getConfigValue($path, $store);
    }

    /*
     * ACL check
     */

    public function _isAllowed() {
        return $this->_authorization->isAllowed('CustomConcepts_Kiyoh::kiyoh');
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute() {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Manage KiyOh Reviews'));
        return $resultPage;
    }

}
