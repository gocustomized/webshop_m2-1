<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace CustomConcepts\Base\Plugin\Model;

class SenderBuilder extends \Magento\Sales\Model\Order\Email\SenderBuilder {
    
    /**
     *
     * @var type \Magento\Sales\Model\Order\Pdf\Invoice
     */
    protected $pdfinvoice;
    /**
     *
     * @var type \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    public function __construct(
            \Magento\Sales\Model\Order\Email\Container\Template $templateContainer, \Magento\Sales\Model\Order\Email\Container\IdentityInterface $identityContainer, \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,  \Magento\Sales\Model\Order\Pdf\Invoice $pdfinvoice , \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
            ) {
        $this->pdfinvoice  = $pdfinvoice;
        $this->scopeConfig =  $scopeConfig;
        parent::__construct($templateContainer, $identityContainer, $transportBuilder);
    }

        public function send() {
       $this->configureEmailTemplate();

        $this->transportBuilder->addTo(
            $this->identityContainer->getCustomerEmail(),
            $this->identityContainer->getCustomerName()
        );

        $copyTo = $this->identityContainer->getEmailCopyTo();

        if (!empty($copyTo) && $this->identityContainer->getCopyMethod() == 'bcc') {
            foreach ($copyTo as $email) {
                $this->transportBuilder->addBcc($email);
            }
        }
        $this->addAttachment();
        $transport = $this->transportBuilder->getTransport();
        $transport->sendMessage();
    }
    
    public function sendCopyTo()
    {
        $copyTo = $this->identityContainer->getEmailCopyTo();

        if (!empty($copyTo) && $this->identityContainer->getCopyMethod() == 'copy') {
            foreach ($copyTo as $email) {
                $this->configureEmailTemplate();

                $this->transportBuilder->addTo($email);
                $this->addAttachment();
                $transport = $this->transportBuilder->getTransport();
                $transport->sendMessage();
            }
        }
    }
    
    public function addAttachment(){
        /**
         * check is this a shipment mail
         */
        if(( $this->templateContainer->getTemplateId() == $this->getConfigValue(\Magento\Sales\Model\Order\Email\Container\ShipmentIdentity::XML_PATH_EMAIL_TEMPLATE) )||( $this->templateContainer->getTemplateId()  == $this->getConfigValue(\Magento\Sales\Model\Order\Email\Container\ShipmentIdentity::XML_PATH_EMAIL_GUEST_TEMPLATE) )){
            
            $templatevars = $this->templateContainer->getTemplateVars();
        
            $order = $templatevars['order'];
            $invoices = $order->getInvoiceCollection();
            $filename = 'invoice-'.$order->getIncrementId().'.pdf';
            $invoicesSet = array();
            foreach ($invoices as $_invoice) {
                array_push($invoicesSet, $_invoice);
            }
            /* Add pdf invoice to the order confirmation email */
            if (count($invoicesSet) > 0) {
                $pdf = $this->pdfinvoice->getPdf($invoicesSet);
            }
            $this->transportBuilder->attachFile($pdf->render(),$filename);
        }
        
    }

    public function getConfigValue($path){
        return $this->scopeConfig->getValue($path,\Magento\Store\Model\ScopeInterface::SCOPE_STORE,0);
    }
    
}   
