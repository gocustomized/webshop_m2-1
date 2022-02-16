<?php
namespace CustomConcepts\Giftsection\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper {

    public function getPopupEditUrl($item){
        $data = unserialize($item->getGocustomizerData());

        $data_value = base64_encode($data['text']);
        return "javascript:openEditPopup({$item->getId()}, '$data_value')";
    }

    public function hasNotecardText($item){
        $data = unserialize($item->getGocustomizerData());
        return isset($data['text']);
    }
}
