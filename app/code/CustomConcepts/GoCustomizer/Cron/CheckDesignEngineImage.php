<?php

namespace CustomConcepts\GoCustomizer\Cron;

class CheckDesignEngineImage {

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $orderCollectionFactory;
    /**
     * @var \CustomConcepts\GoCustomizer\Helper\Data
     */
    protected $gocustomizerHelper;
    /**
     * @var \Magento\Sales\Api\OrderItemRepositoryInterface
     */
    protected $orderItemRepository;
    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @var \CustomConcepts\Base\Helper\Logger 
     */
    protected $logger;
    /**
     * @var \Magento\Sales\Api\Data\OrderStatusHistoryInterfaceFactory
     */
    protected $statuHistoryFactory;

    /**
     * CheckDesignEngineImage constructor.
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     * @param \CustomConcepts\GoCustomizer\Helper\Data $gocustomizerHelper
     * @param \Magento\Sales\Api\OrderItemRepositoryInterface $orderItemRepository
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \CustomConcepts\Base\Helper\Logger $logger
     * @param \Magento\Sales\Api\Data\OrderStatusHistoryInterfaceFactory $statuHistoryFactory
     */
    public function __construct(
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \CustomConcepts\GoCustomizer\Helper\Data $gocustomizerHelper,
        \Magento\Sales\Api\OrderItemRepositoryInterface $orderItemRepository,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \CustomConcepts\Base\Helper\Logger $logger,
        \Magento\Sales\Api\Data\OrderStatusHistoryInterfaceFactory $statuHistoryFactory
    ){
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->gocustomizerHelper = $gocustomizerHelper;
        $this->orderItemRepository = $orderItemRepository;
        $this->orderRepository = $orderRepository;
        $this->scopeConfig = $scopeConfig;
        $this->logger = $logger;
        $this->statuHistoryFactory = $statuHistoryFactory;
    }

    public function execute()
    {
        $this->logger->setLogFile('designengine_cron.log');
        $orders = $this->orderCollectionFactory->create()->addFieldToSelect('*')->addFieldToFilter('status', ['in' => 'waitingfordesign']);

        foreach ($orders as $order){
            $order_items = $order->getItemsCollection();
            foreach ($order_items as $order_item){
                $designEngineUrl = $this->gocustomizerHelper->getProductDesignEngineUrl($order_item);

                if($designEngineUrl){
                    if(!$this->isDesignReady($designEngineUrl)){
                        $this->logger->info('Design not ready for url:' . $designEngineUrl);

                        $order_note = $this->statuHistoryFactory->create();
                        $order_note->setIsCustomerNotified(0);
                        $order_note->setIsVisibleOnFront(0);
                        $order_note->setEntityName('order');

                        $retries = $this->updateOrderItemWithRetry($order_item);
                        if(!$retries){
                            $order->setStatus('design_review');
                            $order_note->setComment('Design is still not ready after 5 attempts.... ');
                            $order->addStatusHistory($order_note);
                            $this->orderRepository->save($order);
                        } else {
                            $order_note->setComment('Design is still not ready after '.$retries.' attempt(s).... ');
                            $order->addStatusHistory($order_note);
                            $this->orderRepository->save($order);
                        }
                    }
                }
            }
        }

    }

    /**
     * @param $url
     * @return bool
     */
    public function isDesignReady($url){
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if($httpCode != 200){
            return false;
        }else{
            return true;
        }
    }

    /**
     * @param $order_item
     * @return bool|int
     */
    public function updateOrderItemWithRetry($order_item){
        $gc_data = unserialize($order_item->getGocustomizerData());
        if(isset($gc_data['retries']) && $gc_data['retries'] >= 5){
            return false;
        }elseif(isset($gc_data['retries'])){
            $gc_data['retries']+=1;
            $order_item->setGocustomizerData(serialize($gc_data));
            $this->orderItemRepository->save($order_item);
            return $gc_data['retries'];
        }else{
            $toggle_support_design_engine = $this->scopeConfig->getValue('gocustomizer/design_engine/toggle_support_design_engine');
            $support_design_engine = $this->scopeConfig->getValue('gocustomizer/design_engine/support_design_engine');
            if($toggle_support_design_engine && $support_design_engine != ''){
                $this->sendMail($gc_data, $order_item);
            }

            $gc_data['retries']=1;
            $order_item->setGocustomizerData(serialize($gc_data));
            $this->orderItemRepository->save($order_item);
            return $gc_data['retries'];
        }
    }

    /**
     * @param $gcdata
     * @param $order_item
     * @throws \Zend_Mail_Exception
     */
    private function sendMail($gcdata,$order_item){
        $body ='Unfortunately the design couldn\'t be find for this item, please check if there is an error. Design: '.$gcdata['design_engine_url'].', Item Id: '.$order_item->getId();
        $mail = new \Zend_Mail();
        $recipients = explode(',' , $this->scopeConfig->getValue('gocustomizer/design_engine/support_design_engine'));
        $mail->addTo($recipients, 'Support Designengine');
        $mail->setBodyText($body);
        $mail->setSubject('First error design_engine url not available.');
        $mail->setFrom('support@gocustomized.com', "Design engine url checker");
        try {
            $mail->send();
        }catch(Exception $e){
            $this->logger->info('Could\'nt sent email because:'.$body);
        }
    }
}
