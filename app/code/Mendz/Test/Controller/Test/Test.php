<?php
namespace Mendz\Test\Controller\Test;


use Magento\Framework\App\Action\Context;
use Magento\Store\Model\ScopeInterface;

class Test extends \Magento\Framework\App\Action\Action
{
    protected $xtentoHelper;
    protected $estimationsHelper;
    protected $orderRepository;
    protected $estimations;
    protected $storeManager;
    protected $scopeConfig;
    protected $date;
    protected $checkoutSession;
    protected $salesOrderApiResource;
    protected $salesOrderApiFactory;
    protected $salesOrderApiInterfaceFactory;
    protected $json;

    public function __construct(
        Context $context,
        \CustomConcepts\XtentoOrderExport\Helper\Data $xtentoHelper,
        \CustomConcepts\Estimations\Helper\Data $estimationsHelper,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \CustomConcepts\Estimations\Helper\Estimation $estimations,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Serialize\Serializer\Json $json
//        \CustomConcepts\Api\Model\ResourceModel\SalesOrderApi $salesOrderApiResource,
//        \CustomConcepts\Api\Model\SalesOrderApiFactory $salesOrderApiFactory
//        \CustomConcepts\Api\Api\Data\SalesOrderApiInterfaceFactory $salesOrderApiInterfaceFactory
    ){
        $this->xtentoHelper = $xtentoHelper;
        $this->estimationsHelper = $estimationsHelper;
        $this->orderRepository = $orderRepository;
        $this->estimations = $estimations;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->date = $date;
        $this->checkoutSession = $checkoutSession;
        $this->json = $json;
//        $this->salesOrderApiResource = $salesOrderApiResource;
//        $this->salesOrderApiFactory = $salesOrderApiFactory;
//        $this->salesOrderApiInterfaceFactory = $salesOrderApiInterfaceFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->orderRepository->get(405);
        $x = $order->getData();
        $y = $order->getMetadata();

        die($y);


//        $x = $this->scopeConfig->getValue('carriers/channelengine/shipping_methods_map');
//        $x = $this->json->unserialize($x);
//        foreach ($x as $y){
//            echo $y['country'] . ' - ' . $y['method_code'] . " - " . $y['shipping_title'] . '</br>';
//        }
//        die();

        //        try {
//            $x = $y['ahaha'];
//        } catch (\Exception $e) {
//            die($e->getTraceAsString());
//        }
//        $salesOrderApi = $this->salesOrderApiInterfaceFactory->create();
//        $salesOrderApi = $this->salesOrderApiFactory->create();
//        $salesOrderApi->setOrderId(1);
//        $salesOrderApi->setApiCallbackUrl('hahahaha');
//        $x = $this->salesOrderApiResource->save($salesOrderApi);
//        die('haha');

//        die(print_r($this->checkoutSession->getData()));


//        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
//        /** @var \Magento\Quote\Api\CartRepositoryInterface $quoteRepository */
//        $quoteRepository = $objectManager->get('Magento\Quote\Api\CartRepositoryInterface');
//        $quote = $quoteRepository->get(93);
//        $created_at = strtotime(date('Y-m-d H:i:s'));
//
//        echo $quote->getUpdatedAt();
//        echo '<br/>';
//        echo $this->date->date($quote->getUpdatedAt())->format('Y-m-d H:i:s');
//        echo '<br/>';
//        echo $this->date->date()->format('Y-m-d H:i:s');
//        echo '<br/>';
//        echo $this->date->getDefaultTimezone();
//        echo '<br/>';
//        echo $this->date->getDefaultTimezonePath();
//        echo '<br/>';
//        echo $this->date->getConfigTimezone();
//        echo '<br/>';
//        die(date('Y-m-d H:i:s'));

//        $storeId = $this->storeManager->getStore()->getId();
//        $shipping_info = $this->scopeConfig->getValue('transsmart_profiles/13964',ScopeInterface::SCOPE_STORE, $storeId);
//
//        echo print_r($shipping_info);
//
//        die();
// ---------------------------------------------------------------------------------------------------

//        $storeId = $this->storeManager->getStore()->getId();
////        $value = $this->scopeConfig->getValue('transsmart/default_shipment_properties/incoterm', ScopeInterface::SCOPE_STORE, $storeId);
//        $value = $this->scopeConfig->getValue('transsmart/default_shipment_properties/incoterm');
//
//        die($storeId);
// ---------------------------------------------------------------------------------------------------

//        $id = '1467230';
//
//        $estimations = $this->estimations->getLatestCalculated($id);
//        $esd = $estimations->getData('shipping_date');
//
//        /** @var \Magento\Sales\Model\Order $order */
//        $order = $this->orderRepository->get($id);
//
//        foreach ($order->getAllVisibleItems() as $item){
//            $product = $item->getProduct();
//
//            if($dateOnStock = $product->getDateOnStock()){
//                if($esd < $dateOnStock){
//                    $esd = $dateOnStock;
//                }
//            }
//
//            die($esd);
//        }
    }
}
