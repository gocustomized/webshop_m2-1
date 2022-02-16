<?php
namespace CustomConcepts\Adyen\Plugin\Gateway\Request\RefundDataBuilder;

class BeforeBuild
{
    public function beforeBuild(\Adyen\Payment\Gateway\Request\RefundDataBuilder $subject, $buildSubject){
        if (isset($buildSubject['amount'])){
            /** @var \Magento\Payment\Gateway\Data\PaymentDataObject $paymentDataObject */
            $paymentDataObject = \Magento\Payment\Gateway\Helper\SubjectReader::readPayment($buildSubject);
            $amountPaid = $paymentDataObject->getPayment()->getAmountPaid();
            $baseAmountPaid = $paymentDataObject->getPayment()->getBaseAmountPaid();
            if(!empty($amountPaid) && !empty($baseAmountPaid)){
                $rate = round($amountPaid / $baseAmountPaid);
                if($rate > 0){
                    $convertedAmount = $buildSubject['amount'] * $rate;
                    $buildSubject['amount'] = number_format($convertedAmount, 2);
                }
            }
        }

        return [$buildSubject];
    }

}
