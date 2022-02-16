<?php

namespace CustomConcepts\Estimations\Helper;

use CustomConcepts\Estimations\Model\EstimationDatesRepository;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Psr\Log\LoggerInterface;

/**
 * Class Estimation
 *
 * @package CustomConcepts\Estimations\Helper
 */
class Estimation extends AbstractHelper
{
    /**
     * @var DeliveryDate
     */
    private $deliveryDateH;
    /**
     * @var ShippingDate
     */
    private $shippingDateH;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var EstimationDatesRepository
     */
    private $estimationDatesRepository;

    /**
     * @param Context $context
     * @param DeliveryDate $deliveryDateH
     * @param ShippingDate $shippingDateH
     * @param LoggerInterface $logger
     * @param EstimationDatesRepository $estimationDatesRepository
     */
    public function __construct(
        Context $context,
        DeliveryDate $deliveryDateH,
        ShippingDate $shippingDateH,
        LoggerInterface $logger,
        EstimationDatesRepository $estimationDatesRepository
    ) {
        parent::__construct($context);
        $this->deliveryDateH = $deliveryDateH;
        $this->shippingDateH = $shippingDateH;
        $this->logger = $logger;
        $this->estimationDatesRepository = $estimationDatesRepository;
    }

    public function get($quote)
    {
//        $this->logger->info('getting delivery estimation from Estimation helper');
        $latest = $this->getLatestCalculated($quote->getId());
        if (!$latest->getId()) {
//            $this->logger->debug('No latest found, calculating', $latest->debug());
            return $this->deliveryDateH->get($quote);
        } else {
//            $this->logger->debug('Latest found,', $latest->debug());
            return $latest;
        }
    }

    public function getLatestCalculated($order_id)
    {
        return $this->estimationDatesRepository->getLatest($order_id);
    }
}
