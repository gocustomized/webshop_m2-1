<?php
namespace CustomConcepts\Giftsection\Block\Cart\Item\Renderer\Actions;

use Magento\Framework\View\Element\Template;

class Edit extends \Magento\Checkout\Block\Cart\Item\Renderer\Actions\Edit
{
    /**
     * @var \CustomConcepts\GoCustomizer\Helper\Data
     */
    protected $ccHelper;

    /**
     * Edit constructor.
     * @param Template\Context $context
     * @param \CustomConcepts\GoCustomizer\Helper\Data $ccHelper
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \CustomConcepts\GoCustomizer\Helper\Data $ccHelper,
        array $data = []
    ){
        parent::__construct($context, $data);
        $this->ccHelper = $ccHelper;
    }

    /**
     * @return string execute js link
     */
    public function getPopupEditUrl()
    {
        $data = unserialize($this->getItem()->getGocustomizerData());

        $data_value = base64_encode($data['text']);
        return "javascript:openEditPopup({$this->getItem()->getId()}, '$data_value')";
    }

    /**
     * @return bool
     */
    public function hasNotecardText(){
        $data = unserialize($this->getItem()->getGocustomizerData());
        return isset($data['text']);
    }

    /**
     * @return \CustomConcepts\GoCustomizer\Helper\URL|string
     */
    public function getEditLink(){
        $editUrl = '';
        if($this->hasNotecardText()) {
            $editUrl = $this->getPopupEditUrl();
        } elseif($this->ccHelper->isGocustomizerProduct($this->getItem())){
            $editUrl = $this->ccHelper->getCustomizerEditUrl($this->getItem()->getProduct()->getId(), $this->getItem()->getId());
        } elseif($this->isProductVisibleInSiteVisibility()) {
            $editUrl = $this->escapeUrl($this->getConfigureUrl());
        }

        return $editUrl;
    }
}
