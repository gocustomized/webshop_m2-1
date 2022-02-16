<?php
declare(strict_types=1);

namespace CustomConcepts\Tableratezipranges\Service;

use Bluebirdday\TranssmartSmartConnect\Model\Quote\AddressManager;
use CustomConcepts\Tableratezipranges\Api\TablerateGeneratorInterface;
use Magento\Checkout\Model\Session;
use Magento\OfflineShipping\Model\ResourceModel\Carrier\TablerateFactory;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateRequestFactory;
use Magento\Quote\Model\QuoteRepository;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class TablerateGenerator implements TablerateGeneratorInterface
{
    /**
     * @var RateRequestFactory
     */
    private $rateRequestFactory;

    /**
     * @var Session
     */
    private $checkoutSession;

    /**
     * @var QuoteRepository
     */
    private $quoteRepository;

    /**
     * @var TablerateFactory
     */
    private $tablerateFactory;

    /**
     * @var AddressManager
     */
    private $addressManager;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param RateRequestFactory $rateRequestFactory
     * @param Session $checkoutSession
     * @param QuoteRepository $quoteRepository
     * @param TablerateFactory $tablerateFactory
     * @param AddressManager $addressManager
     * @param StoreManagerInterface $storeManager
     * @param LoggerInterface $logger
     */
    public function __construct(
        RateRequestFactory $rateRequestFactory,
        Session $checkoutSession,
        QuoteRepository $quoteRepository,
        TablerateFactory $tablerateFactory,
        AddressManager $addressManager,
        StoreManagerInterface $storeManager,
        LoggerInterface $logger
    ) {
        $this->rateRequestFactory = $rateRequestFactory;
        $this->checkoutSession = $checkoutSession;
        $this->quoteRepository = $quoteRepository;
        $this->tablerateFactory = $tablerateFactory;
        $this->addressManager = $addressManager;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
    }

    /**
     * @return array
     */
    public function getRates(): array
    {
        try {
            $rates = $this->tablerateFactory->create()->getRates($this->getRateRequest());
        } catch (\Exception $exception) {
            $rates = [];
            $this->logger->error($exception->getMessage());
        }

        return $rates ?: [];
    }

    /**
     * @return RateRequest
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getRateRequest() : RateRequest
    {
        $quoteId = $this->checkoutSession->getQuoteId();
        $quote = $this->quoteRepository->getActive($quoteId);
        $address = $this->addressManager->getPickupAddress($quote)
            ?: $quote->getShippingAddress();
        $storeId = $quote->getStoreId();
        $websiteId = $this->storeManager->getStore($storeId)->getWebsiteId();
        $request = $this->rateRequestFactory->create();
        if ($address && $storeId) {
            $request->setAllItems($address->getAllItems());
            $request->setWebsiteId($websiteId);
            $request->setConditionName(
                $this->storeManager->getWebsite($websiteId)->getConfig('carriers/tablerate/condition_name')
            );
            $request->setDestCountryId($address->getCountryId());
            $request->setDestRegionId($address->getRegionId());
            $request->setDestPostcode($address->getPostcode());
            $request->setPackageValue($address->getBaseSubtotal());
            $request->setPackageValueWithDiscount($address->getBaseSubtotalWithDiscount());
            $request->setPackageWeight($address->getWeight());
            $request->setPackageQty($address->getItemQty());
        }

        return $request;
    }
}
