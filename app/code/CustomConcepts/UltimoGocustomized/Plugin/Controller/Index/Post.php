<?php

namespace CustomConcepts\UltimoGocustomized\Plugin\Controller\Index;

class Post {
    public function afterExecute(\Magento\Contact\Controller\Index\Post $subject, $result){
        return $result->setRefererUrl('*/*/');
    }
    
}