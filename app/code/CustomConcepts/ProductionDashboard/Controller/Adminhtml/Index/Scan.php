<?php
namespace CustomConcepts\ProductionDashboard\Controller\Adminhtml\Index;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;

class Scan extends \Magento\Backend\App\Action
{
    protected $jsonFactory;
    protected $orderItemRepository;
    protected $customerGroupRepository;
    protected $addressConfig;
    protected $logger;

    public function __construct(
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Magento\Sales\Api\OrderItemRepositoryInterface $orderItemRepository,
        \Magento\Customer\Api\GroupRepositoryInterface $customerGroupRepository,
        \Magento\Customer\Model\Address\Config $addressConfig,
        \Psr\Log\LoggerInterface $logger,
        Context $context
    ){
        $this->jsonFactory = $jsonFactory;
        $this->orderItemRepository = $orderItemRepository;
        $this->customerGroupRepository = $customerGroupRepository;
        $this->addressConfig = $addressConfig;
        $this->logger = $logger;
        parent::__construct($context);
    }

    public function execute()
    {
        $result = $this->jsonFactory->create();

        $barcodeScan = $this->getRequest()->getParam('barcode_scan');

        try {
            $data = $this->loadItemData($barcodeScan);
            $result->setData($data);
        } catch (LocalizedException $exception) {
            $this->logger->alert($exception->getMessage());
            $result->setHttpResponseCode(400);
            $result->setData(['errorMessage' => $exception->getMessage()]);
        } catch (\RuntimeException $exception) {
            $this->logger->alert($exception->getMessage());
            $result->setHttpResponseCode(400);
            $result->setData(['errorMessage' => __('Cannot perform request. See log for details.')]);
        }


        return $result;
    }

    private function getOrderItem($itemId){
        return $this->orderItemRepository->get($itemId);
    }

    protected function loadItemData($barcodeScan){
        /** @var $orderItem \Magento\Sales\Model\Order\Item */
        $orderItem = $this->getOrderItem($barcodeScan);
        /** @var $order \Magento\Sales\Model\Order */
        $order = $orderItem->getOrder();

        $renderer = $this->addressConfig->getFormatByCode('html')->getRenderer();

        /** ORDER INFORMATION */
        $data['store'] = $order->getStore()->getCode();
        $data['order_date'] = date('Y-m-d H:m:s', strtotime($order->getCreatedAt()));
        $data['increment_id'] = $order->getIncrementId();
        $data['shipping_method'] = $order->getShippingDescription();
        $data['status'] = $order->getStatus();

        $orderStatusHistories = $order->getStatusHistoryCollection()
            ->addFieldToSelect('comment')
            ->setOrder('entity_id', 'ASC') /** TODO: sort is not working */
            ->getData();

        $data['order_notes'] = [];
        foreach ($orderStatusHistories as $orderStatusHistory){
            $data['order_notes'][] = $orderStatusHistory['comment'];
        }

        /** CLIENT INFO */
        $data['customer_group'] = $this->customerGroupRepository->getById($order->getCustomerGroupId())->getCode();
        $data['customer_email'] = $order->getCustomerEmail();
        $data['shipping_address'] = $renderer->renderArray($order->getShippingAddress());

        $orderItems = $order->getItems();
        $data['other_items'] = [];
        foreach ($orderItems as $item){
            if($item->getId() != $orderItem->getId()){
                $data['other_items'][] = $item->getId();
            }
        }

        /** ITEM INFO */
        $product = $orderItem->getProduct();
        $data['item_status'] = $orderItem->getStatus()->getText();
        $data['item_supplier'] = $product->getAttributeText('supplier') ?: '';
        $data['item_color'] = $product->getAttributeText('customproduct_color') ?: '';
//        $data['stock_item_sku'] = $product->getCustomAttribute('stock_item_sku') ? $product->getCustomAttribute('stock_item_sku')->getValue() : $product->getSku();
        $data['item_sku'] = $product->getSku();
        $data['qty'] = $orderItem->getQtyOrdered() ?: 0;
        $data['item_name'] = $product->getName() ?: '';


        /**
         * THUMBNAIL AND PRINT FILE
         * can be retrieved in gocustomizer_data as thumb and design
         */
        $data['options'] = unserialize($orderItem->getGocustomizerData());

        return $data;
    }

}
