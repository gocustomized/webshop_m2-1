<?php
/**
 * Anowave Magento 2 Google Tag Manager Enhanced Ecommerce (UA) Tracking
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Anowave license that is
 * available through the world-wide-web at this URL:
 * http://www.anowave.com/license-agreement/
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category 	Anowave
 * @package 	Anowave_Ec
 * @copyright 	Copyright (c) 2020 Anowave (http://www.anowave.com/)
 * @license  	http://www.anowave.com/license-agreement/
 */

namespace Anowave\Ec\Plugin;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Response\Http;

class JsFooterPlugin
{
    const XML_PATH_DEV_MOVE_JS_TO_BOTTOM = 'dev/js/move_script_to_bottom';
    
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;
    
    /**
     * Set helper 
     * 
     * @var \Anowave\Ec\Helper\Data
     */
    protected $helper;
    
    /**
     * Constructor 
     * 
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct
    (
        ScopeConfigInterface $scopeConfig,
        \Anowave\Ec\Helper\Data $helper
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->helper = $helper;
    }
    
    /**
     * Around before 
     * 
     * @param \Magento\Theme\Controller\Result\JsFooterPlugin $plugin
     * @param callable $proceed
     * @param Http $subject
     */
    public function aroundBeforeSendResponse(\Magento\Theme\Controller\Result\JsFooterPlugin $plugin, callable $proceed, Http $subject)
    {
        if (!$this->helper->isActive())
        {
            return $proceed($subject);
        }
        /**
         * Response content 
         * 
         * @var string $content
         */
        $content = $subject->getContent();
        
        /**
         * Script map 
         * 
         * @var array $script
         */
        $script = [];

        if (strpos($content, '</body') !== false) 
        {
            if ($this->scopeConfig->isSetFlag(static::XML_PATH_DEV_MOVE_JS_TO_BOTTOM,ScopeInterface::SCOPE_STORE)) 
            {
                /**
                 * Skip scripts criteria 
                 * 
                 * @var array $skip
                 */
                $skip =
                [
                    'data-ommit',
                    'www.googletagmanager.com/gtm.js',
                    'require.js',
                    'require.min.js',
                    'requirejs-min-resolver.js',
                    'requirejs-min-resolver.min.js',
                    'requirejs-config.js',
                    'requirejs-config.min.js',
                    'mixins.js',
                    'mixins.min.js',
                    'ec.js',
                    'ec.min.js',
                    'ec4.js',
                    'ec4.min.js',
                    'var BASE_URL'
                ];
                
                /**
                 * Match scripts in response 
                 * 
                 * @var string $pattern
                 */
                $pattern = '#<script[^>]*+(?<!text/x-magento-template.)>.*?</script>#is';
                
                $content = preg_replace_callback($pattern,function ($matchPart) use (&$script, &$skip) 
                {
                    
                    foreach ($skip as $value)
                    {
                        /**
                         * Prevent script from being moved to bottom
                         */
                        if (false !== strpos($matchPart[0], $value))
                        {
                            return $matchPart[0];
                        }
                    }
  
                    $script[] = $matchPart[0];
                    
                    return '';
                    
                },$content);
                
                $subject->setContent(str_replace('</body', implode("\n", $script) . "\n</body", $content));
            }
        }
    }
}