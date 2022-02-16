<?php
namespace CustomConcepts\Newsletter\Model;

class Subscriber extends \Magento\Newsletter\Model\Subscriber
{
    public function sendConfirmationRequestEmail(){ return $this; }

    public function sendConfirmationSuccessEmail(){ return $this; }

    public function sendUnsubscriptionEmail(){ return $this; }
}
