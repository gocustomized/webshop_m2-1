<?php
namespace CustomConcepts\Giftsection\Controller\Cart;

use Magento\Framework\App\Action\Context;

class Add extends \Magento\Framework\App\Action\Action {

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;
    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $cartRepositoryInterface;
    /**
     * @var \Magento\Quote\Api\Data\CartItemInterfaceFactory
     */
    protected $cartItemInterface;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManagerInterface;
    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress
     */
    protected $remoteAddress;
    /**
     * @var \Magento\Framework\Session\SessionManager
     */
    protected $sessionManager;
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;
    /**
     * @var \CustomConcepts\Base\Helper\Logger
     */
    protected $logger;

    protected $serializer;

    /**
     * Add constructor.
     * @param Context $context
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Quote\Api\CartRepositoryInterface $cartRepositoryInterface
     * @param \Magento\Quote\Api\Data\CartItemInterfaceFactory $cartItemInterface
     * @param \Magento\Store\Model\StoreManagerInterface $storeManagerInterface
     * @param \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress
     * @param \Magento\Framework\Session\SessionManager $sessionManager
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \CustomConcepts\Base\Helper\Logger $logger
     */
    public function __construct(
        Context $context,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Quote\Api\CartRepositoryInterface $cartRepositoryInterface,
        \Magento\Quote\Api\Data\CartItemInterfaceFactory $cartItemInterface,
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
        \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress,
        \Magento\Framework\Session\SessionManager $sessionManager,
        \Magento\Checkout\Model\Session $checkoutSession,
        \CustomConcepts\Base\Helper\Logger $logger,
        \Magento\Framework\Serialize\Serializer\Json $serializer
    ){
        parent::__construct($context);
        $this->productRepository = $productRepository;
        $this->cartRepositoryInterface = $cartRepositoryInterface;
        $this->cartItemInterface = $cartItemInterface;
        $this->storeManagerInterface = $storeManagerInterface;
        $this->remoteAddress = $remoteAddress;
        $this->sessionManager = $sessionManager;
        $this->checkoutSession = $checkoutSession;
        $this->logger = $logger;
        $this->serializer = $serializer;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
        /**
         * Sample request:
         * request = [   {'sku' => 23453423, type='gift'},
         *               {'sku' => 23453423,  type='note', text => {block_1 => 'blabalbalba' , block_2 => 'balbalblaba2'}}
         *               {'sku' => 23453423,  type='note', text => [block_1 => 'blabalbalba' , block_2 => 'balbalblaba2'}, 'item_id' => 123209}
         *              ]
         */
    {
        $this->logger->setLogFile('giftsection.log');
        $request_data = json_decode($this->getRequest()->getParam('request_data'),true);
//        $request_data = [
//            ['sku' => 'iphone-screenprotect', 'type' => 'gift'],
//            ['sku' => '90204110407', 'type' => 'gift'],
//            ['sku' => '91324100706', 'text' => ["asdasdasd"], 'type' => 'note']
//        ];

        $cart = $this->checkoutSession->getQuote();
        $cart->setStoreId($this->storeManagerInterface->getStore()->getId());
        $cart->setRemoteIp($this->remoteAddress->getRemoteAddress());
        $session = md5(uniqid());
        $option = $this->serializer->serialize(['session' => $session]);
        foreach($request_data as $request){
            $this->logger->info(json_encode($request));
            $product = $this->productRepository->get($request['sku']);

            switch ($request['type']){
                case 'note':
                        $quote_item = $this->addNoteForCard($product, $request, $option);
                        if(empty($request['item_id'])){
                            $cart->addItem($quote_item);
                        }
                    break;
                case 'gift':
                        $quote_item = $this->cartItemInterface->create()->setProduct($product)->setQty(1);
                        $quote_item->addOption(['label' => 'info_buyRequest', 'code' => 'info_buyRequest', 'value' => $option]);
                        $cart->addItem($quote_item);
                    break;
            }

        }

        $this->cartRepositoryInterface->save($cart);
    }

    /**
     * @param $product
     * @param $productRequest
     * @param $session
     * @return bool|false|\Magento\Quote\Model\Quote\Item
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function addNoteForCard($product, $productRequest, $option){
        $gocustomizer_data = serialize(
            array(
//                'design' => $design_filename,
                'text' => json_encode($productRequest['text'])
            )
        );

        $itemId = isset($productRequest['item_id']) ? $productRequest['item_id'] : '';
        if(!empty($itemId)){
            $quote_item = $this->checkoutSession->getQuote()->getItemById($itemId)->setProduct($product);
        } else {
            $quote_item = $this->cartItemInterface->create()->setProduct($product)->setQty(1);
        }

        $quote_item->setGocustomizerData($gocustomizer_data);
        $quote_item->addOption(['label' => 'info_buyRequest', 'code' => 'info_buyRequest', 'value' => $option]);

        return $quote_item;
    }
}
