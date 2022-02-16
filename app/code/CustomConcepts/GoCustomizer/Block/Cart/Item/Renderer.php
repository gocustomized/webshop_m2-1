<?php

/**
 * CustomConcepts_GoCustomizer extension
 * @category  CustomConcepts_Extensions
 * @package   CustomConcepts_GoCustomizer
 * @copyright Copyright (c) 2017
 * @author GoCustomized <info@gocustomized.com>
 */

namespace CustomConcepts\GoCustomizer\Block\Cart\Item;

use CustomConcepts\GoCustomizer\Helper\Data as CC_Data;
use Magento\Catalog\Block\Product\ImageBuilder;
use Magento\Catalog\Helper\Product\Configuration;
use Magento\Checkout\Model\Session;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Module\Manager;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Url\Helper\Data as Url_Data;
use Magento\Framework\View\Element\Message\InterpretationStrategyInterface;
use Magento\Framework\View\Element\Template\Context;

class Renderer extends \Magento\Checkout\Block\Cart\Item\Renderer{

    /**
     *
     * @var type \CustomConcepts\GoCustomizer\Helper\Data
     */
    protected $ccHelper;

    protected $_storeManager;

    protected $giftSectionHelper;

    /**
     *
     * @param Context $context
     * @param Configuration $productConfig
     * @param Session $checkoutSession
     * @param ImageBuilder $imageBuilder
     * @param Url_Data $urlHelper
     * @param ManagerInterface $messageManager
     * @param PriceCurrencyInterface $priceCurrency
     * @param Manager $moduleManager
     * @param InterpretationStrategyInterface $messageInterpretationStrategy
     * @param Data $ccHelper
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        Context $context,
        Configuration $productConfig,
        Session $checkoutSession,
        ImageBuilder $imageBuilder,
        Url_Data $urlHelper,
        ManagerInterface $messageManager,
        PriceCurrencyInterface $priceCurrency,
        Manager $moduleManager,
        InterpretationStrategyInterface $messageInterpretationStrategy,
        CC_Data $ccHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \CustomConcepts\Giftsection\Helper\Data $giftSectionHelper,
        array $data = array()
    ) {
        $this->ccHelper = $ccHelper;
        $this->_storeManager = $storeManager;
        $this->giftSectionHelper = $giftSectionHelper;
        parent::__construct($context, $productConfig, $checkoutSession, $imageBuilder, $urlHelper, $messageManager, $priceCurrency, $moduleManager, $messageInterpretationStrategy, $data);
    }
    /**
     * Return Edit link
     * @return string
     */
    public function editLink(){
        if($this->giftSectionHelper->hasNotecardText($this->_item)){ //notecard popup
            return $this->giftSectionHelper->getPopupEditUrl($this->_item);
        }elseif($this->ccHelper->isGocustomizerProduct($this->_item)){ //gocustomizer edit
            return $this->ccHelper->getCustomizerEditUrl($this->_item->getProduct()->getId(), $this->_item->getId());
        }else{ //configuration edit
            $configParams = ['id' => $this->_item->getId(),
                'product_id' => $this->_item->getProduct()->getId()];
           return $this->getUrl('checkout/cart/configure', $configParams);
        }
    }
    /**
     * Get product thumbnail
     * @return boolean
     */
    public function getProductThumbnail()
    {
        $img_block = $this->getImage($this->getProductForThumbnail(), 'cart_page_product_thumbnail');
        if($this->ccHelper->isGocustomizerProduct($this->_item)){
            $gocustomizerdata = unserialize($this->_item->getGocustomizerData());
            if(!empty($gocustomizerdata['thumb'])){
                $img_block->setData('image_url', $gocustomizerdata['thumb'].'?t='.time());
            }
        }
        return $img_block;
    }

    public function getMediaUrl($path)
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . $path;
    }
}
