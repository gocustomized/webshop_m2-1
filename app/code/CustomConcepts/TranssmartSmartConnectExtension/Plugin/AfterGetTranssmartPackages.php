<?php
namespace CustomConcepts\TranssmartSmartConnectExtension\Plugin;

class AfterGetTranssmartPackages
{
    public function afterGetData(\Magento\Framework\Model\AbstractExtensibleModel $subject, $result, $key = ''){
        /** to retrieve old serialized data in the DB */
        if($key == 'transsmart_packages'){
            if($result){
                $data = @unserialize($result);
                if($data){
                    return $data;
                }
            } else { //return empty json for null/empty fields to avoid serialization error
                return '{}';
            }
        }

        return $result;
    }
}
