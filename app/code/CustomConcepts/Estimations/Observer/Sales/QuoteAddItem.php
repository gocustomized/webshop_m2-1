<?php

namespace CustomConcepts\Estimations\Observer\Sales;

use CustomConcepts\Estimations\Helper\Estimation;
use Magento\Checkout\Model\Session;
use Magento\Framework\Event\Observer;

/**
 * Class QuoteAddItem
 *
 * @package CustomConcepts\Estimations\Observer\Sales
 */
class QuoteAddItem implements \Magento\Framework\Event\ObserverInterface
{

    /**
     * @var Estimation
     */
    private $estimation;
    /**
     * @var Session
     */
    private $checkoutSession;

    public function __construct(
        Estimation $estimation,
        Session $checkoutSession
    ) {
        $this->estimation = $estimation;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * Execute observer
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(
        Observer $observer
    ) {
        $quote = $observer->getEvent()->getQuoteItem()->getQuote();
        $estimation = $this->estimation->get($quote);
        $this->checkoutSession->setEstimationShippingDate($estimation->getShippingDate());
    }
}
