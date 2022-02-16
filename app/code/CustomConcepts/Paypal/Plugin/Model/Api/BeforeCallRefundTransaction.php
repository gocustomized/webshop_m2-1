<?php
namespace CustomConcepts\Paypal\Plugin\Model\Api;

class BeforeCallRefundTransaction
{
    /**
     * @var \Magento\Directory\Helper\Data
     */
    protected $currencyHelper;

    public function __construct(
        \Magento\Directory\Helper\Data $currencyHelper
    ){
        $this->currencyHelper = $currencyHelper;
    }

    public function beforeCallRefundTransaction(\Magento\Paypal\Model\Api\Nvp $subject){
        $order = $subject->getPayment()->getOrder();
        $baseCurrencyCode = $order->getBaseCurrencyCode();
        $orderCurrencyCode = $order->getOrderCurrencyCode();

        if($baseCurrencyCode != $orderCurrencyCode){
            $subject->setCurrencyCode($orderCurrencyCode);
            $subject->setAmount(round($this->currencyHelper->currencyConvert($subject->getAmount(), $baseCurrencyCode, $orderCurrencyCode), 2));
        }
    }
}
