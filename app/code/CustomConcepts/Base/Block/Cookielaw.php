<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace CustomConcepts\Base\Block;

class Cookielaw extends \Magento\Framework\View\Element\Template{

    const XML_PATH_IS_ENABLE = 'cookielaw/cookielawGroup/show_banner';
    const XML_PATH_PRIVACY_MESSAGE = 'cookielaw/cookielawGroup/privacy_message';
    const XML_PATH_PRIVACY_URL = 'cookielaw/cookielawGroup/privacy_url';
    const XML_PATH_PRIVACY_URL_TEXT = 'cookielaw/cookielawGroup/privacy_url_text';
    const XML_PATH_CSS = 'cookielaw/cookielawGroup/css';
    const XML_PATH_POSITION = 'cookielaw/cookielawGroup/position';
    const XML_PATH_COOKIE_LIFETIME = 'cookielaw/cookielawGroup/cookie_lifetime';
    const XML_PATH_FADEOUT = 'cookielaw/cookielawGroup/fadeout';
    
    /**
     *
     * @var type \Magento\Framework\App\Config\ScopeConfigInterface 
     */
    protected $scopeConfig;
    
    /**
     * 
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context, 
        array $data = array()
    ){
        $this->scopeConfig = $context->getScopeConfig();
        parent::__construct($context, $data);
    }
    
    public function getConfigValue($path,$store = 0){
        return $this->scopeConfig->getValue($path,\Magento\Store\Model\ScopeInterface::SCOPE_STORE,$store);
    }
    
    public function isEnable(){
        return $this->getConfigValue(self::XML_PATH_IS_ENABLE);
    }
    
    public function privacyMessage(){
        return $this->getConfigValue(self::XML_PATH_PRIVACY_MESSAGE);
    }
    
    public function privacyUrl(){
        return $this->getConfigValue(self::XML_PATH_PRIVACY_URL);
    }
    
    public function privacyUrlText(){
        return $this->getConfigValue(self::XML_PATH_PRIVACY_URL_TEXT);
    }
    
    public function cssStyling(){
        return $this->getConfigValue(self::XML_PATH_CSS);
    }
    
    public function getPosition(){
        return $this->getConfigValue(self::XML_PATH_POSITION);
    }
    
    public function cookieLifetime(){
        return $this->getConfigValue(self::XML_PATH_COOKIE_LIFETIME);
    }
    
    public function fadeOut(){
        return $this->getConfigValue(self::XML_PATH_FADEOUT);
    }
    
}