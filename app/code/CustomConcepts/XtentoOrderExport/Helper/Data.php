<?php
namespace CustomConcepts\XtentoOrderExport\Helper;


use Magento\Framework\App\Helper\Context;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    public static function getGoCustomizerData($gocustomizerData, $dataField = ''){
        if($dataField){
            $data = unserialize($gocustomizerData);
            if(isset($data[$dataField])){
                return $data[$dataField];
            }
        }
        return '';
    }

    public static function getNoteCardTextBlock($gocustomizerData, $index){
        $data = unserialize($gocustomizerData);
        $index -= 1;
//        $data = unserialize('a:1:{s:4:"text";s:123:"["aaaaaaaaaaaaaaaaaaaaaa","bbbbbbbbbbbbbbbbbbbbb","cccccccccccccccccccccccc","dddddddddddddddddddd","eeeeeeeeeeeeeeeeeeee"]";}');

        if(isset($data['text'])){
            $textblocks = json_decode($data['text']);

            $search  = array('&'    ,  '<'   ,'>', '"');
            $replace = array('&amp;', '&lt;', '&gt;', '&quot;');

            return isset($textblocks[$index]) ? str_replace($search,$replace,$textblocks[$index]) : '';
        }

        return '';
    }

    public static function getOrderEsd($order_id){
        $_objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        /** @var \CustomConcepts\Estimations\Helper\Estimation $estimations */
        $estimations = $_objectManager->get('\CustomConcepts\Estimations\Helper\Estimation');
        $estimations = $estimations->getLatestCalculated($order_id);

        return date("Y-m-d H:i:s", strtotime($estimations->getData('shipping_date')));
    }

    public static function checkOos($item_id){
        $_objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        /** @var \CustomConcepts\Estimations\Helper\ShippingDate $shippingDateHelper */
        $shippingDateHelper = $_objectManager->get('\CustomConcepts\Estimations\Helper\ShippingDate');

        return $shippingDateHelper->checkOos($item_id);
    }

    public static function getShippingConfigData($shipping_method, $data = null, $store_id = 0){
        $_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var \CustomConcepts\Estimations\Helper\Data $estimationsHelper */
        $estimationsHelper = $_objectManager->get('\CustomConcepts\Estimations\Helper\Data');

        $shipping_method_arr = explode('_', $shipping_method);
        $carrier_id = end($shipping_method_arr);

        $shipping_info = $estimationsHelper->getShippingMethodConfig($carrier_id, $store_id);

        if($data){
            return isset($shipping_info[$data]) ? $shipping_info[$data] : '';
        } else {
            return $shipping_info;
        }
    }
}
