<?php
namespace CustomConcepts\Base\ViewModel;

class CheckoutSuccessViewModel implements \Magento\Framework\View\Element\Block\ArgumentInterface
{

    /** @var \Magento\Sales\Model\Order $order */
    protected $order;

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    protected $serializer;

    /**
     * @var \CustomConcepts\GoCustomizer\Helper\Data
     */
    protected $gcHelper;

    protected $estimationHelper;

    protected $date;

    protected $currentOrder;

    public function __construct(
        \Magento\Sales\Api\Data\OrderInterface $order,
        \Magento\Framework\Serialize\SerializerInterface $serializer,
        \CustomConcepts\GoCustomizer\Helper\Data $gcHelper,
        \CustomConcepts\Estimations\Helper\Estimation $estimationHelper,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date
    ){
        $this->order = $order;
        $this->serializer = $serializer;
        $this->gcHelper = $gcHelper;
        $this->estimationHelper = $estimationHelper;
        $this->date = $date;
    }

    public function setOrderByIncrementId($increment_id){
        $this->currentOrder = $this->order->loadByIncrementId($increment_id);
    }

    /**
     * @param $increment_id
     * @return bool|string
     */
    public function getCustomDataLayer($increment_id = null){
        if($increment_id){
            $order = $this->order->loadByIncrementId($increment_id);
        } else {
            $order = $this->currentOrder;
        }

        $skus = [];
        foreach ($order->getAllItems() as $orderItem){
            if ($orderItem->getParentItemId()) {
                continue;
            }
            $skus[] = $this->gcHelper->getProductSku($orderItem);
        }

        $dataLayer = [
            'skus' => $skus,
            'currency' => $order->getBaseCurrencyCode(),
            'revenue' => number_format($order->getGrandTotal(), 2),
            'order' => $increment_id,
            'c_mail' => $order->getCustomerEmail()
        ];

        return $this->serializer->serialize($dataLayer);
    }

    public function getEsdMessage($increment_id = null){
        if($increment_id){
            $order = $this->order->loadByIncrementId($increment_id);
        } else {
            $order = $this->currentOrder;
        }

        $estimations = $this->estimationHelper->getLatestCalculated($order->getId());
        $esd = $estimations->getData('shipping_date') ? $this->date->formatDate($estimations->getData('shipping_date'), \IntlDateFormatter::FULL, false) : null;
        $eddMin = $estimations->getData('min_delivery_date') ? $this->date->formatDate($estimations->getData('min_delivery_date'), \IntlDateFormatter::FULL, false) : null;
        $eddMax = $estimations->getData('max_delivery_date') ? $this->date->formatDate($estimations->getData('max_delivery_date'), \IntlDateFormatter::FULL, false) : null;

        if($eddMin && $eddMax){
            if($eddMin == $eddMax){
                $message = __("We expect to deliver your order on %1.", $eddMin);
            } else {
                $message = __("We expect to deliver your order around %1 and %2.", $eddMin, $eddMax);
            }
        } else {
            $message = __("We expect to ship your order on %1.", $esd);
        }

        return $message;
    }
}
