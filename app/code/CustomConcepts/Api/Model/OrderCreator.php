<?php
namespace CustomConcepts\Api\Model;

use Magento\Framework\Exception\LocalizedException;

class OrderCreator implements \CustomConcepts\Api\Api\OrderCreatorInterface
{
    protected $restRequest;
    protected $orderRepository;
    protected $clientFactory;
    protected $productRepository;
    protected $customerRepository;
    protected $addressRepository;
    protected $cartRepository;
    protected $cartItemRepository;
    protected $quoteItemFactory;
    protected $quoteItemResourceModel;
    protected $invoiceService;
    protected $transaction;
    protected $consumer;
    protected $integrationFactory;


    public function __construct(
        \Magento\Framework\Webapi\Rest\Request $restRequest,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \CustomConcepts\Api\Model\Api\ClientFactory $clientFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository,
        \Magento\Quote\Api\CartRepositoryInterface $cartRepository,
        \Magento\Quote\Api\CartItemRepositoryInterface $cartItemRepository,
        \Magento\Quote\Model\Quote\ItemFactory $quoteItemFactory,
        \Magento\Quote\Model\ResourceModel\Quote\Item $quoteItemResourceModel,
        \Magento\Sales\Model\Service\InvoiceService $invoiceService,
        \Magento\Framework\DB\Transaction $transaction,
        \Magento\Integration\Model\Oauth\Consumer $consumer,
        \Magento\Integration\Model\IntegrationFactory $integrationFactory
    ){
        $this->restRequest = $restRequest;
        $this->orderRepository = $orderRepository;
        $this->clientFactory = $clientFactory;
        $this->productRepository = $productRepository;
        $this->customerRepository = $customerRepository;
        $this->addressRepository = $addressRepository;
        $this->cartRepository = $cartRepository;
        $this->cartItemRepository = $cartItemRepository;
        $this->quoteItemFactory = $quoteItemFactory;
        $this->quoteItemResourceModel = $quoteItemResourceModel;
        $this->invoiceService = $invoiceService;
        $this->transaction = $transaction;
        $this->consumer = $consumer;
        $this->integrationFactory = $integrationFactory;
    }

    public function createOrders(){
        $response = [];
        $requests = $this->restRequest->getBodyParams();
        foreach ($requests as $request){
            $orderRes = $this->createOrder($request);
            $response[] = $orderRes[0];
        }
        return $response;
    }

    public function createOrder($data = null)
    {
        $response = [];
        $request = $data ?: $this->restRequest->getBodyParams();
        /** @var \CustomConcepts\Api\Model\Api\Client $client */
        $client = $this->clientFactory->create();

        $token = $this->getAuthorizationToken();
        $client->setToken($token);
        if(isset($request['consumer_key'])){
            $customerId = $this->getApiUserId($request['consumer_key']);
            $customer = $this->customerRepository->getById($customerId);
        } else {
            throw new LocalizedException(__('Consumer Key cannot be empty'));
        }

        /** Create Quote */
        $quoteRequest = ['customer_id' => $customerId];
        $quoteId = $client->createQuote(json_encode($quoteRequest));
        $quote = $this->cartRepository->getActive($quoteId);

        /** Add Items to Cart */
        $cartItems = $this->processItems($request, $quoteId);
        foreach ($cartItems as $cartItem){
            $item = $client->addItemToCart(json_encode($cartItem));
            $itemId = $item['item_id'];
            $response['items_id'][] = $itemId;
            $this->setQuoteItemData($itemId, $request, $quote);
        }

        /** Add Shipping Information(Address and shipping method) */
        $shippingInfo = $this->processShippingInformation($request, $customer);
        $shippingInfo['cart_id'] = $quoteId;
        $client->addShippingInfo(json_encode($shippingInfo));

        /** Set payment method and create order */
        $orderRequest = [
            'cart_id' => $quoteId,
            'paymentMethod' => [
                'method' => 'purchaseorder',
                'po_number' => $request['partner_reference']
            ]
        ];
        $orderId = $client->createOrder(json_encode($orderRequest));

        /** Set additional data for order */
        $order = $this->orderRepository->get($orderId);
        $response['order_id'] = $order->getIncrementId();
        $order->setApiOrderId($request['partner_reference']);
        $this->orderRepository->save($order);
        $this->createInvoice($order);


        return [$response];
    }

    public function getAuthorizationToken(){
        $authHeader = $this->restRequest->getHeader('Authorization');
        return str_replace('Bearer ', '', $authHeader);
    }

    public function getApiUserId($consumerKey){
        $consumer = $this->consumer->loadByKey($consumerKey);
        $integration = $this->integrationFactory->create()->load($consumer->getId(), 'consumer_id');
        if($integration->getCustomerId()){
            return $integration->getCustomerId();
        } else {
            throw new LocalizedException(__('Invalid Consumer key.'));
        }
    }

    public function processItems($request, $quoteId){
        $items = [];
        if (isset($request['lines'])){
            $lines = $request['lines'];
            foreach ($lines as $line){
                $product = $this->productRepository->get($line['item_sku']);
                $cartItem = [
                    'cartItem' => [
                        'sku' => $product->getSku(),
                        'qty' => isset($line['qty']) ? $line['qty'] : 1,
                        'name' => $product->getName(),
                        'price' => $product->getPrice(),
                        'product_type' => $product->getTypeId(),
                        'quote_id' => "$quoteId",
                        'product_option' => [
                            'extension_attributes' => [
                                'custom_options' => []
                            ]
                        ]
                    ]
                ];
                $items[] = $cartItem;
            }
        }
        return $items;
    }

    /**
     * @param $request array
     * @param $customer \Magento\Customer\Api\Data\CustomerInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function processShippingInformation($request, $customer){
        if(isset($request['address'])){
            $billingAddressId = $customer->getDefaultBilling();
            $customerBillingAddress = $this->addressRepository->getById($billingAddressId);

            $shippingInfoRequest = [
                'addressInformation' => [
                    'shipping_carrier_code' => 'transsmartdelivery',
                    'shipping_method_code' => 'delivery_' . $request['shipping']
                ]
            ];

            /** Shipping Address */
            $shippingStreet = [];
            $index = 1;
            while(isset($request['address']["address$index"])){
                $shippingStreet[] = $request['address']["address$index"];
                $index+=1;
            }
            $requestAddress = $request['address'];
            $shippingAddress = [
                'country_id' => $requestAddress['country_code'],
                'city' => $requestAddress['city'],
                'company' => '',
                'email' => $requestAddress['email'],
                'firstname' => $requestAddress['firstname'],
                'lastname' => $requestAddress['lastname'],
                'postcode' => $requestAddress['zipcode'],
                'street' => $shippingStreet,
                'telephone' => $requestAddress['phone']
            ];
            $shippingInfoRequest['addressInformation']['shippingAddress'] = $shippingAddress;

            /** Billing Address */
            $billingStreet = [];
            foreach ($customerBillingAddress->getStreet() as $street){
                $billingStreet[] = $street;
            }
            $billingAddress = [
                'country_id' => $customerBillingAddress->getCountryId(),
                'city' => $customerBillingAddress->getCity(),
                'company' => $customerBillingAddress->getCompany(),
                'email' => $customer->getEmail(),
                'firstname' => $customerBillingAddress->getFirstname(),
                'lastname' => $customerBillingAddress->getLastname(),
                'postcode' => $customerBillingAddress->getPostcode(),
                'street' => $billingStreet,
                'telephone' => $customerBillingAddress->getTelephone()
            ];
            $shippingInfoRequest['addressInformation']['billingAddress'] = $billingAddress;

            return $shippingInfoRequest;
        }
    }

    public function setQuoteItemData($itemId, $request, $quote){
        $quoteItem = $this->quoteItemFactory->create();
        $this->quoteItemResourceModel->load($quoteItem, $itemId);
        $quoteItem->setQuote($quote);
        $customizerData = [];
        $sku = $quoteItem->getSku();
        if (isset($request['lines'])){
            foreach ($request['lines'] as $line){
                if($line['item_sku'] == $sku){
                    $customizerData = [
                        'thumb' => $line['image_url'],
                        'design' => $line['image_url'],
                        'api_item_id' => $line['identifier']
                    ];
                }
            }
            $quoteItem->setGocustomizerData(serialize($customizerData));
            $this->quoteItemResourceModel->save($quoteItem);
        }
    }

    /**
     * @param $order \Magento\Sales\Model\Order
     */
    public function createInvoice($order){
        $invoiceItems = [];
        foreach ($order->getAllVisibleItems() as $item){
            $invoiceItems[$item->getId()] = $item->getQtyOrdered();
        }

        if (!$order->canInvoice()) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('The order does not allow an invoice to be created.')
            );
        }
        $invoice = $this->invoiceService->prepareInvoice($order, $invoiceItems);
        $invoice->setShippingAmount($order->getShippingAmount());
        $invoice->setSubtotal($order->getSubtotal());
        $invoice->setBaseSubtotal($order->getBaseSubtotal());
        $invoice->setGrandTotal($order->getGrandTotal());
        $invoice->setBaseGrandTotal($order->getGrandTotal());
        $invoice->setCommentText('Auto genereted invoice from API.');
        $invoice->register();
        $transactionSave = $this->transaction->addObject($invoice)->addObject($invoice->getOrder());
        $transactionSave->save();
    }
}
