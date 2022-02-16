<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Razoyo\AnimalProfile\Controller\Profile;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\View\Result\PageFactory;

class View extends \Magento\Framework\App\Action\Action
{
    /**
     * @var ScustomerSession
     */
    private $customerSession;

    /**
     * @var PageFactory
     */
    private $pageFactory;

    public function __construct(
        Context $context,
        Session $customerSession,
        PageFactory $pageFactory
    ) {
        parent::__construct($context);
//        parent::__construct($context, $customerSession);
        $this->customerSession = $customerSession;
        $this->pageFactory = $pageFactory;
    }

    /**
     * Dispatch request
     *
     * @param RequestInterface $request
     * @return ResponseInterface
     * @throws NotFoundException
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->customerSession->authenticate()) {
            $this->_actionFlag->set('', 'no-dispatch', true);
        }
        return parent::dispatch($request);
    }

    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $resultPage = $this->pageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Animal Profile'));

        return $resultPage;
    }
}
