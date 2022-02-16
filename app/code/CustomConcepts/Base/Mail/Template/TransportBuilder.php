<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace CustomConcepts\Base\Mail\Template;

class TransportBuilder extends \Magento\Framework\Mail\Template\TransportBuilder {

    /**
     * @param 
     */
    public function attachFile($file, $name = '') {
        if (!empty($file)) {
            $this->message->createAttachment(
                    $file, \Zend_Mime::TYPE_OCTETSTREAM, \Zend_Mime::DISPOSITION_ATTACHMENT, \Zend_Mime::ENCODING_BASE64, basename($name)
            );

            return $this;
        }
        return true;
    }

}
