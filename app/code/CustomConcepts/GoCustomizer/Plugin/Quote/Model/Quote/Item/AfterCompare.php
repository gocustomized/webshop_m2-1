<?php
namespace CustomConcepts\GoCustomizer\Plugin\Quote\Model\Quote\Item;

use Magento\Quote\Model\Quote\Item;

class AfterCompare
{
    public function afterCompare(\Magento\Quote\Model\Quote\Item\Compare $subject, $result, Item $target, Item $compared){
        if($result){
            if($target->getGocustomizerData() != $compared->getGocustomizerData()){
                return false;
            }
        }

        return $result;
    }
}
