<?php
namespace CustomConcepts\Adyen\Plugin\Gateway\Request\CheckoutDataBuilder;

use Adyen\Payment\Observer\AdyenHppDataAssignObserver;

class AfterBuild
{
    protected $subjectReader;
    protected $adyenHelper;

    public function __construct(
        \Magento\Payment\Gateway\Helper\SubjectReader $subjectReader,
        \Adyen\Payment\Helper\Data $adyenHelper
    ){
        $this->subjectReader = $subjectReader;
        $this->adyenHelper = $adyenHelper;
    }

    public function afterBuild(\Adyen\Payment\Gateway\Request\CheckoutDataBuilder $subject, $result, $buildSubject){
        $paymentDataObject = $this->subjectReader->readPayment($buildSubject);
        $payment = $paymentDataObject->getPayment();

        if ($this->adyenHelper->isPaymentMethodOpenInvoiceMethod(
                $payment->getAdditionalInformation(AdyenHppDataAssignObserver::BRAND_CODE)
            ) || $this->adyenHelper->isPaymentMethodAfterpayTouchMethod(
                $payment->getAdditionalInformation(AdyenHppDataAssignObserver::BRAND_CODE)
            ) || $this->adyenHelper->isPaymentMethodOneyMethod(
                $payment->getAdditionalInformation(AdyenHppDataAssignObserver::BRAND_CODE)
            )
        ) {
            if(isset($result['body']['lineItems'])){
                foreach ($result['body']['lineItems'] as &$lineItem){
                    $amountIncludingTax = $lineItem['amountIncludingTax'];
                    $taxPercentage = $lineItem['taxPercentage'] / 10000; //convert it back to percent. eg(0.25)

                    $amountExcludingTax = $amountIncludingTax / 1 + $taxPercentage;
                    $lineItem['amountExcludingTax'] = $amountExcludingTax;
                    $lineItem['taxAmount'] = $amountIncludingTax - $amountExcludingTax;
                }
            }
        }

        return $result;
    }
}
