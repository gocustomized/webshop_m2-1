<?php

/**
 * CustomConcepts_ReorderDiscountCode extension
 * @category  CustomConcepts_Extensions
 * @package   CustomConcepts_ReorderDiscountCode
 * @copyright Copyright (c) 2017
 * @author CustomConcepts <info@CustomConcepts.com>
 */
namespace CustomConcepts\ReorderDiscountCode\Plugin\Model;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class CouponManagement extends \Magento\Quote\Model\CouponManagement {

    /**
     * Quote repository.
     *
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

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
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\SalesRule\Model\CouponFactory $couponFactory
     */
    public function __construct(\Magento\Quote\Api\CartRepositoryInterface $quoteRepository, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\SalesRule\Model\CouponFactory $couponFactory
    ) {
        parent::__construct($quoteRepository);
        $this->quoteRepository = $quoteRepository;
        $this->couponFactory = $couponFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * Overwrite set method to add our validation to check valid coupons for storewise
     * @param \Magento\Quote\Model\CouponManagement $subject
     * @param \CustomConcepts\ReorderDiscountCode\Plugin\Model\callable $proceed
     * @param type $cartId
     * @param type $couponCode
     * @return boolean
     * @throws NoSuchEntityException
     * @throws CouldNotSaveException
     */
    public function aroundSet(\Magento\Quote\Model\CouponManagement $subject, callable $proceed, $cartId, $couponCode) {
        /** @var  \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);
        if (!$quote->getItemsCount()) {
            throw new NoSuchEntityException(__('Cart %1 doesn\'t contain products', $cartId));
        }
        /* Specific code hack for stor view coupons */
        $storeId = $this->storeManager->getStore()->getId();
        $ruleStoreId = 0;
        $codeLength = strlen($couponCode);
        //get rule's store Id from the coupon code
        if ($codeLength) {
            $oCoupon = $this->couponFactory->create()->loadByCode($couponCode);
            $ruleStoreId = $oCoupon->getData('store_id');
        }

        $quote->getShippingAddress()->setCollectShippingRates(true);

        try {
            //check the store id for specific coupon
            if (($ruleStoreId > 0 && $ruleStoreId == $storeId) || $ruleStoreId == 0) {
                $quote->setCouponCode($couponCode);
                $this->quoteRepository->save($quote->collectTotals());
            }
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Could not apply coupon code'));
        }
        if ($quote->getCouponCode() != $couponCode) {
            throw new NoSuchEntityException(__('Coupon code is not valid'));
        }
        return true;
    }

}
