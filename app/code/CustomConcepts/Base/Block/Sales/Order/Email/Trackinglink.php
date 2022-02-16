<?php

namespace CustomConcepts\Base\Block\Sales\Order\Email;

use Magento\Framework\App\Config\ScopeConfigInterface;
use \Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config;

class Trackinglink extends \Magento\Framework\View\Element\Template
{

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;
    protected $orderRepository;

    /**
     * @var Config
     */
    private $config;

    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        OrderRepositoryInterface $orderRepository,
        Config $config,
        array $data = []
    ){
        $this->scopeConfig = $scopeConfig;
        $this->orderRepository = $orderRepository;
        $this->config = $config;
        parent::__construct($context,$data);
    }
    public function getOrder()
    {
        $order = $this->getData('order');

        if ($order !== null) {
            return $order;
        }
        $orderId = (int)$this->getData('order_id');
        if ($orderId) {
            $order = $this->orderRepository->get($orderId);
            $this->setData('order', $order);
        }

        return $this->getData('order');
    }

    public function getTrackAndTraceIds(){
        return $this->scopeConfig->getValue('shipping/track_and_trace_info/transsmart_final_service_level_ids', ScopeConfigInterface::SCOPE_TYPE_DEFAULT);

    }

    public function getTrackAndTraceInfoText($storeId){
        return $this->config->getValue('shipping/track_and_trace_info/track_and_trace_info_text', ScopeInterface::SCOPE_STORE, $storeId);
    }

}
