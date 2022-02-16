<?php

/**
 * CustomConcepts_ReorderDiscountCode extension
 * @category  CustomConcepts_Extensions
 * @package   CustomConcepts_ReorderDiscountCode
 * @copyright Copyright (c) 2017
 * @author CustomConcepts <info@CustomConcepts.com>
 */

namespace CustomConcepts\ReorderDiscountCode\Plugin\Controller\Cart;

class CouponPost extends \Magento\Checkout\Controller\Cart\CouponPost {

    /**
     *
     * @var \Magento\SalesRule\Model\CouponFactory 
     */
    protected $couponFactory;

    /**
     *
     * @var \Magento\Store\Model\StoreManagerInterface 
     */
    protected $storeManager;
    /**
     * 
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Magento\Checkout\Model\Cart $cart
     * @param \Magento\SalesRule\Model\CouponFactory $couponFactory
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     */
    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Magento\Checkout\Model\Session $checkoutSession, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator, \Magento\Checkout\Model\Cart $cart, \Magento\SalesRule\Model\CouponFactory $couponFactory, \Magento\Quote\Api\CartRepositoryInterface $quoteRepository) {
        parent::__construct($context, $scopeConfig, $checkoutSession, $storeManager, $formKeyValidator, $cart, $couponFactory, $quoteRepository);
        $this->couponFactory = $couponFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * Check the coupon with the store specific and validate
     * @param \Magento\Checkout\Controller\Cart\CouponPost $subject
     * @param \CustomConcepts\ReorderDiscountCode\Plugin\Controller\Cart\callable $proceed
     * @return type
     */
    public function aroundExecute(\Magento\Checkout\Controller\Cart\CouponPost $subject, callable $proceed) {

        $couponCode = $this->getRequest()->getParam('remove') == 1 ? '' : trim($this->getRequest()->getParam('coupon_code'));

        $cartQuote = $this->cart->getQuote();
        $oldCouponCode = $cartQuote->getCouponCode();

        $codeLength = strlen($couponCode);
        if (!$codeLength && !strlen($oldCouponCode)) {
            return $this->_goBack();
        }

        try {
            $isCodeLengthValid = $codeLength && $codeLength <= \Magento\Checkout\Helper\Cart::COUPON_CODE_MAX_LENGTH;
            /* Specific code hack for stor view coupons */
            $storeId = $this->storeManager->getStore()->getId();
            $ruleStoreId = 0;

            //get rule's store Id from the coupon code
            if ($codeLength) {
                $oCoupon = $this->couponFactory->create()->loadByCode($couponCode);
                $ruleStoreId = $oCoupon->getData('store_id');
            }

            $itemsCount = $cartQuote->getItemsCount();
            if ($itemsCount) {
                $cartQuote->getShippingAddress()->setCollectShippingRates(true);
                if (($ruleStoreId > 0 && $ruleStoreId == $storeId) || $ruleStoreId == 0) {
                    $cartQuote->setCouponCode($isCodeLengthValid ? $couponCode : '')->collectTotals();
                    $this->quoteRepository->save($cartQuote);
                }
            }

            if ($codeLength) {
                $escaper = $this->_objectManager->get(\Magento\Framework\Escaper::class);
                $coupon = $this->couponFactory->create();
                $coupon->load($couponCode, 'code');
                if (!$itemsCount) {
                    if ($isCodeLengthValid && $coupon->getId()) {
                        $this->_checkoutSession->getQuote()->setCouponCode($couponCode)->save();
                        $this->messageManager->addSuccess(
                                __(
                                        'You used coupon code "%1".', $escaper->escapeHtml($couponCode)
                                )
                        );
                    } else {
                        $this->messageManager->addError(
                                __(
                                        'The coupon code "%1" is not valid.', $escaper->escapeHtml($couponCode)
                                )
                        );
                    }
                } else {
                    if ($isCodeLengthValid && $coupon->getId() && $couponCode == $cartQuote->getCouponCode()) {
                        $this->messageManager->addSuccess(
                                __(
                                        'You used coupon code "%1".', $escaper->escapeHtml($couponCode)
                                )
                        );
                    } else {
                        $this->messageManager->addError(
                                __(
                                        'The coupon code "%1" is not valid.', $escaper->escapeHtml($couponCode)
                                )
                        );
                    }
                }
            } else {
                $this->messageManager->addSuccess(__('You canceled the coupon code.'));
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addError(__('We cannot apply the coupon code.'));
            $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
        }

        return $this->_goBack();
    }

}
