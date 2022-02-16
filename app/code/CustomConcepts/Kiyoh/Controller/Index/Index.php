<?php

/**
 * CustomConcepts_Kiyoh extension
 * @category  CustomConcepts_Extensions
 * @package   CustomConcepts_Kiyoh
 * @copyright Copyright (c) 2017
 * @author GoCustomized <info@gocustomized.com>
 */

namespace CustomConcepts\Kiyoh\Controller\Index;

use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Framework\App\Action\Action {

    /**
     *
     * @var type \CustomConcepts\Kiyoh\Model\AbstractKiyohModel
     */
    protected $abstractModel;
    protected $_resultPage;

    /**
     * 
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Session\Generic $generic
     */
    public function __construct(
    \Magento\Framework\App\Action\Context $context, \CustomConcepts\Kiyoh\Model\AbstractKiyohModel $abstractModel, PageFactory $resultPage
    ) {
        $this->abstractModel = $abstractModel;
        $this->_resultPage = $resultPage;
        parent::__construct($context);
    }

    public function execute() {
        $enabled = $this->abstractModel->getConfigValue('kiyoh/general/enabled');
        $overview = $this->abstractModel->getConfigValue('kiyoh/overview/enabled');

        if ($enabled && $overview) {
            $result = $this->_resultPage->create();
//            if ($title = $this->abstractModel->getConfigValue('kiyoh/overview/meta_title')):
//                $result->getConfig()->getTitle()->set($title); //setting the page
//            endif;

            if ($description = $this->abstractModel->getConfigValue('kiyoh/overview/meta_description')):
                $result->getConfig()->setDescription($description); // set meta description
            endif;

            if ($keywords = $this->abstractModel->getConfigValue('kiyoh/overview/meta_keywords')):
                $result->getConfig()->setKeywords($keywords); // set meta keyword
            endif;

            return $result;
        } else {
            $this->_redirect('/');
        }
    }

}
