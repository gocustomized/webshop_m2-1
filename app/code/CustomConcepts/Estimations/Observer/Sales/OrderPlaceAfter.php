<?php


namespace CustomConcepts\Estimations\Observer\Sales;

use CustomConcepts\Estimations\Helper\Estimation;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class QuoteAddItem
 *
 * @package CustomConcepts\Estimations\Observer\Sales
 */
class OrderPlaceAfter implements ObserverInterface
{

    /**
     * @var Estimation
     */
    private $estimation;

    public function __construct(Estimation $estimation)
    {

        $this->estimation = $estimation;
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
        $quote = $observer->getEvent()->getOrder();
        $this->estimation->get($quote);
    }
}

