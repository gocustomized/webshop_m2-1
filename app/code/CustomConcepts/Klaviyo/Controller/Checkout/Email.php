<?php
namespace CustomConcepts\Klaviyo\Controller\Checkout;


class Email extends \Klaviyo\Reclaim\Controller\Checkout\Email
{
    protected $checkoutSession;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Checkout\Model\Session $checkoutSession
    ){
        $this->checkoutSession = $checkoutSession;
        parent::__construct($context, $resultJsonFactory);
    }

    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        return $result->setData(['success' => true]);
    }
}
