<?php
namespace CustomConcepts\GoCustomizer\Plugin\Minicart;


class AfterGetItemData
{
    protected $gcHelper;
    protected $checkoutSession;

    public function __construct(
        \CustomConcepts\GoCustomizer\Helper\Data $gcHelper,
        \Magento\Checkout\Model\Session $checkoutSession
    ){
        $this->gcHelper = $gcHelper;
        $this->checkoutSession = $checkoutSession;
    }

    public function afterGetItemData(\Magento\Checkout\CustomerData\AbstractItem $subject, $result){
        if (isset($result['item_id'])){
            $item = $this->getQuoteItem($result['item_id']);
            $thumbnail = $this->gcHelper->getProductThumbnail($item);
            if($thumbnail){
                $result['product_image']['src'] = $thumbnail;
            }
        }
        return $result;
    }

    private function getQuoteItem($itemId){
        $quote = $this->checkoutSession->getQuote();
        return $quote->getItemById($itemId);
    }

}
