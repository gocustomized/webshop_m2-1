<?php
namespace CustomConcepts\GoCustomizer\Plugin\Checkout\Model\Cart;


class AfterGetImages
{
    protected $gcHelper;
    protected $itemRepository;

    public function __construct(
        \Magento\Quote\Api\CartItemRepositoryInterface $itemRepository,
        \CustomConcepts\GoCustomizer\Helper\Data $gcHelper
    ){
        $this->gcHelper = $gcHelper;
        $this->itemRepository = $itemRepository;
    }

    public function afterGetImages(\Magento\Checkout\Model\Cart\ImageProvider $subject, $result, $cartId){
        $items = $this->itemRepository->getList($cartId);

        foreach ($items as $cartItem){
            $thumbnail = $this->gcHelper->getProductThumbnail($cartItem);
            if($thumbnail){
                $result[$cartItem->getItemId()]['src'] = $thumbnail;
            }
            $result[$cartItem->getItemId()]['width'] = 150;
            $result[$cartItem->getItemId()]['height'] = 150;
        }

        return $result;
    }

}
