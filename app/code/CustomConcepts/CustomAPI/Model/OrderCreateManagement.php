<?php

namespace CustomConcepts\CustomAPI\Model;

use CustomConcepts\CustomAPI\Api\OrderCreateManagementInterface;
use Magento\Integration\Model\Oauth\Consumer;

class OrderCreateManagement implements OrderCreateManagementInterface {

    protected $orderManagement;
    protected $_integrationFactory;
    protected $_consumer;
    protected $customer;
    protected $customerRepository;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var \Magento\Framework\HTTP\Client\Curl
     */
    protected $_curl;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerModelFactory;

    /**
     * @var \Magento\Quote\Model\QuoteIdMaskFactory
     */
    private $quoteIdMaskFactory;

    /**
     * @var QuoteFactory
     */
    protected $quoteFactory;

    /**
     * @var QuoteFactory
     */
    protected $quoteItemFactory;

    /**
     * @var QuoteFactory
     */
    protected $cartRepositoryInterface;

    public function __construct(
    \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Framework\App\Filesystem\DirectoryList $directoryList, \Magento\Framework\Filesystem\Io\File $file, \Magento\Framework\Filesystem\Driver\File $fileDriver, \Magento\Quote\Api\CartRepositoryInterface $cartRepositoryInterface, \Magento\Quote\Model\Quote\Item $quoteItemFactory, \Magento\Quote\Model\QuoteFactory $quoteFactory, \Magento\Quote\Api\CartRepositoryInterface $quoteRepository, \Magento\Customer\Model\CustomerFactory $customerModelFactory, \Magento\Framework\HTTP\Client\Curl $curl, \Magento\Sales\Model\Order $orderManagement, \Magento\Integration\Model\IntegrationFactory $_integrationFactory, Consumer $_consumer, \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
    ) {
        $this->_curl = $curl;
        $this->orderManagement = $orderManagement;
        $this->_integrationFactory = $_integrationFactory;
        $this->_consumer = $_consumer;
        $this->customerRepository = $customerRepository;
        $this->quoteRepository = $quoteRepository;
        $this->customerModelFactory = $customerModelFactory;
        $this->quoteFactory = $quoteFactory;
        $this->quoteItemFactory = $quoteItemFactory;
        $this->cartRepositoryInterface = $cartRepositoryInterface;
        $this->directoryList = $directoryList;
        $this->file = $file;
        $this->fileDriver = $fileDriver;
        $this->storeManager = $storeManager;
    }

    /**
     * GET for CartCreate api
     * @param string 
     * @return string
     */
    public function createApiOrder($data) {

        $apiData = json_decode($data, true);
        try {
            //first of all check authenticated user and get API customer_id details
            $_verifyAPIuser = $this->getApiUser($apiData['consumerkey']);
            
            //if user verifieD process further
            if ($_verifyAPIuser && $this->customer->getEmail() == $apiData['username']) {
                return json_encode(['entity_id' => $this->customer->getId(), 'store_id' => $this->customer->getStoreId()]);
            } else {
                return false;
            }
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Cannot create quote'));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function assignCartCustomer($quoteId, $customerId, $storeId) {

        $customer = $this->customerRepository->getById($customerId);

        try {
            $quote = $this->quoteFactory->create()->load($quoteId);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            /** @var \Magento\Quote\Model\Quote $quote */
            $quote = $this->quoteFactory->create();
        }
        try {
            $quote->setStoreId($storeId);
            $quote->setCustomer($customer);
            $quote->setCustomerIsGuest(0);
            $this->quoteRepository->save($quote);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Cannot create quote'));
        }
        return true;
    }

    /**
     * Process quoteitem to save the customizer data
     * @param string 
     * @return string
     */
    public function processCustomizerItem($customizerItem) {
        try {
            $itemData = json_decode($customizerItem, true);

            //first of all check authenticated user and get API customer_id details
            $quote = $this->quoteFactory->create()->load($itemData['quote_id']);
            $quoteItem = $quote->getItemById($itemData['item_id']);
            if (!$quoteItem) {
                return false;
            }
            //upload image
            $thumb = $this->uploadImage($itemData['options']['preview_url'], 'thumb');

            $design = $this->uploadImage($itemData['options']['design_url'], 'design');

            $gocustomizer_data = serialize([
                'design' => $design,
                'thumb' => $thumb,
                'session' => $itemData['token'],
                'position' => 'front', //$position,
                'x_offset' => 10, //$x_offset,
                'y_offset' => 10, //$y_offset,
                'supplier_sku' => 10, //$y_offset,
                    ]
            );
            $quoteItem->setGocustomizerData($gocustomizer_data);
            $quoteItem->save();
            $quote->setTotalsCollectedFlag(false);

            $customer = $this->customerRepository->getById($itemData['customer_id']);
            $quote->setStoreId($itemData['store_id']);
            $quote->setCustomer($customer);
            $quote->setCustomerIsGuest(0);
            $quote->save();
        } catch (\Exception $e) {
            return __($e->getMessage());
        }
        return true;
    }

    public function uploadImage($imagePath, $type = "thumb") {

        $path = ($type == 'thumb') ? '/customer_data/orders/thumbs/' . date("Ymd") . '/' : '/customer_data/orders/' . date("Ymd") . '/';
        $thumbnail_path = $path . basename($imagePath);

        /** get mediaDir */
        $mediaDir = $this->directoryList->getPath('media');
        /** check and create folder if exists */
        $this->file->checkAndCreateFolder($mediaDir . $path);
        //echo $mediaDir . $thumbnail_path;die;
        /** @file_put_content * */
        if (!$this->fileDriver->isExists($mediaDir . $thumbnail_path)) {
            $this->fileDriver->filePutContents($mediaDir . $thumbnail_path, $this->fileDriver->fileGetContents($imagePath), 0777);
        } else {
            $this->fileDriver->deleteFile($mediaDir . $thumbnail_path);
            $this->fileDriver->filePutContents($mediaDir . $thumbnail_path, $this->fileDriver->fileGetContents($imagePath), 0777);
        }

        return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . $thumbnail_path;
    }

    /**
     * Process quoteitem to save the customizer data
     * @param string 
     * @return bool
     */
    public function getApiUser($consumerkey) {
        $consumer = $this->_consumer->loadByKey($consumerkey);
        if (!empty($consumer)) {
            //first of all check authenticated user and get API customer_id details
            $integration = $this->_integrationFactory->create()->load($consumer->getId(), 'consumer_id');
            $this->setApiCustomer($integration->getCustomerId());
            if (isset($this->customer))
                return true;
            else
                return false;
        }
        else {
            return false;
        }
    }

    /**
     * Get the customizer data
     * @return customer object|bool
     */
    public function getApiCustomer() {
        if (isset($this->customer)) {
            return $this->customer;
        }
        return false;
    }

    /**
     * Set customer object
     * @param int $customerId 
     */
    public function setApiCustomer($customerId) {
        if (!isset($this->customer)) {
            $this->customer = $this->customerRepository->getById($customerId);
        }
    }

    /**
     * Check the order is new
     * @param string 
     * @return bool
     */
    public function verifyNewOrder($apiOrderId) {

        //load the order by reference order id
        try {
            $order = $this->orderManagement->loadByAttribute('api_order_id', $apiOrderId);
        } catch (\Exception $e) {
            return __($e->getMessage());
        }
        if ($order->getId()) {
            //if existing order 
            return 0;
        } else {
            //if new order
            return 1;
        }
    }

    /**
     * Assign an order API number to newly created order.
     *
     * @param string $apiOrderId API Order ID.
     * @param int $orderId The Order ID.
     * @return string
     */
    public function addAPIOrderId($apiOrderId, $orderId) {
        //load the order by reference order id
        try {
            $order = $this->orderManagement->load($orderId);

            $incrementId = $order->getIncrementId();
            $order->setApiOrderId($apiOrderId);
            $order->save();
            return $incrementId;
        } catch (\Exception $e) {
            return __($e->getMessage());
        }
    }

}
