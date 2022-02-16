<?php
namespace CustomConcepts\Giftsection\Plugin\Quote\Item;


class AfterRepresentProduct
{
    protected $jsonSerializer;

    public function __construct(
        \Magento\Framework\Serialize\Serializer\Json $jsonSerializer
    ){
        $this->jsonSerializer = $jsonSerializer;
    }

    public function afterRepresentProduct(\Magento\Quote\Model\Quote\Item $subject, $result, $product){
        if($result){
            $itemOptions = $subject->getOptionsByCode();
            $productOptions = $product->getCustomOptions();

            $option1 = $this->jsonSerializer->unserialize($itemOptions['info_buyRequest']->getValue());
            $option2 = $this->jsonSerializer->unserialize($productOptions['info_buyRequest']->getValue());
            if(isset($option1['session']) && isset($option2['session'])){
                if($option1['session'] != $option2['session']){
                    return false;
                }
            }
        }

        return $result;
    }
}
