<?php
namespace CustomConcepts\CustomerAccount\Plugin\Magento\Sales\Helper\Reorder;


class AfterCanReorder
{
    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;
    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $date;

    /**
     * AfterCanReorder constructor.
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date
     */
    public function __construct(
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date
    ){
        $this->orderRepository = $orderRepository;
        $this->date = $date;
    }

    /**
     * @param \Magento\Sales\Helper\Reorder $subject
     * @param $result
     * @param $orderId
     * @return false
     */
    public function afterCanReorder(\Magento\Sales\Helper\Reorder $subject, $result, $orderId){
        if($result){
            $order = $this->orderRepository->get($orderId);
            $orderDate = $this->date->date($order->getCreatedAt());
            $currentDate = $this->date->date();

            /** NOTE: current computation includes H:i:s */
            $dateDiff = date_diff($orderDate, $currentDate);

            /** TODO: this can be a system config */
            if($dateDiff->days > 30) {
                return false;
            }
        }

        return $result;
    }
}
