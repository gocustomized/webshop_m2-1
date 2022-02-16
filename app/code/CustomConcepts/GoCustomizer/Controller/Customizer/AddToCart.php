<?php

/**
 * CustomConcepts_GoCustomizer extension
 * @category  CustomConcepts_Extensions
 * @package   CustomConcepts_GoCustomizer
 * @copyright Copyright (c) 2017
 * @author GoCustomized <info@gocustomized.com>
 */

namespace CustomConcepts\GoCustomizer\Controller\Customizer;

use Magento\Framework\Controller\Result\JsonFactory;

class AddToCart extends \Magento\Framework\App\Action\Action {

    /**
     *
     * @var type \Magento\Checkout\Model\Cart
     */
    protected $checkoutCart;

    /**
     *
     * @var type \Magento\Framework\App\Filesystem\DirectoryList
     */
    protected $directoryList;

    /**
     *
     * @var type \Magento\Framework\Filesystem\Io\File
     */
    protected $file;

    /**
     *
     * @var type \Magento\Framework\Filesystem\Driver\File
     */
    protected $fileDriver;

    /**
     *
     * @var type \Magento\Catalog\Model\ProductFactory
     */
    protected $catalogProductFactory;

    /**
     *
     * @var type \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     *
     * @var type \Magento\Framework\Session\Generic
     */
    protected $generic;

    /**
     *
     * @var type \Magento\Quote\Model\Quote\ItemFactory
     */
    protected $quoteItemFactory;

    /**
     *
     * @var type \Magento\Framework\DataObjectFactory
     */
    protected $dataObjectFactory;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_url;
    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param JsonFactory $resultJsonFactory
     * @param \Magento\Checkout\Model\Cart $checkoutCart
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directoryList
     * @param \Magento\Framework\Filesystem\Io\File $file
     * @param \Magento\Framework\Filesystem\Driver\File $fileDriver
     * @param \Magento\Catalog\Model\ProductFactory $catalogProductFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Session\Generic $generic
     * @param \Magento\Quote\Model\Quote\ItemFactory $quoteItemFactory
     * @param \Magento\Framework\DataObjectFactory $dataObjectFactory
     */
    public function __construct(
    \Magento\Framework\App\Action\Context $context,        JsonFactory $resultJsonFactory
        , \Magento\Checkout\Model\Cart $checkoutCart, \Magento\Framework\App\Filesystem\DirectoryList $directoryList, \Magento\Framework\Filesystem\Io\File $file, \Magento\Framework\Filesystem\Driver\File $fileDriver, \Magento\Catalog\Model\ProductFactory $catalogProductFactory, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Framework\Session\Generic $generic, \Magento\Quote\Model\Quote\ItemFactory $quoteItemFactory, \Magento\Framework\DataObjectFactory $dataObjectFactory
    ) {
        $this->directoryList = $directoryList;
        $this->checkoutCart = $checkoutCart;
        $this->file = $file;
        $this->fileDriver = $fileDriver;
        $this->catalogProductFactory = $catalogProductFactory;
        $this->storeManager = $storeManager;
        $this->generic = $generic;
        $this->quoteItemFactory = $quoteItemFactory;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->_url = $context->getUrl();
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);

    }

    public function execute() {
        $cart = $this->checkoutCart;
        $result = $this->resultJsonFactory->create();
        $sku = $this->getRequest()->getParam('sku');
        $crosssells = $this->getRequest()->getParam('crosssells');
        //$design_filename = $this->getRequest()->getParam('design');
        $thumbnail = $this->getRequest()->getParam('thumbnail');
        $session_id = $this->getRequest()->getParam('customizer_session');
        $position = $this->getRequest()->getParam('position');
        $x_offset = $this->getRequest()->getParam('x_offset');
        $y_offset = $this->getRequest()->getParam('y_offset');
        $customer_auth = $this->getRequest()->getParam('customer_auth');
        $path = '/customer_data/orders/thumbs/' . date("Ymd") . '/';
        $thumbnail_path = $path . $session_id . '.png';
//        echo '<pre>';print_r($this->getRequest()->getParams());die;
        try {
            /** get mediaDir */
            $mediaDir = $this->directoryList->getPath('media');
            /** check and create folder if exists */
            $this->file->checkAndCreateFolder($mediaDir . $path);
            //echo $mediaDir . $thumbnail_path;die;
            /** @file_put_content * */
            if (!$this->fileDriver->isExists($mediaDir . $thumbnail_path)) {
                $this->fileDriver->filePutContents($mediaDir . $thumbnail_path, base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $thumbnail)), 0777);
            } else {
                $this->fileDriver->deleteFile($mediaDir . $thumbnail_path);
                $this->fileDriver->filePutContents($mediaDir . $thumbnail_path, base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $thumbnail)), 0777);
            }

            $product = $this->catalogProductFactory->create()->loadByAttribute('sku', $sku);

            if (!empty($product)) {
                //$cart->reinitializeState();
                $gocustomizer_data = serialize([
//                    'design' => $design_filename,
                    'thumb' => $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . $thumbnail_path,
                    'session' => $session_id,
                    'customer_auth' => $customer_auth,
                    'position' => 'front', //$position,
                    'x_offset' => 10, //$x_offset,
                    'y_offset' => 10, //$y_offset,
                    'supplier_sku' => 10, //$y_offset,
                        ]
                );
                $itemId = $this->generic->getItemId();
                if (!empty($itemId)) {
                    // $this->logger->debug('Item id not empty getting it from cart.Item id:'.$itemId);
                    $quoteItem = $cart->getQuote()->getItemById($itemId)->setProduct($product);
                    $this->generic->unsetData('item_id');
                } else {
                    //$this->logger->debug('No quote itemId found creating new one.');
                    //$quoteItem = $this->quoteItemFactory->create()->setProduct($product)->addQty(1)->addOption($this->dataObjectFactory->create(array('qty' => 1,'product'=>$product)));
                    $quoteItem = $cart->getQuote()->addProduct($product, $this->dataObjectFactory->create(array('qty' => 1)));
                }

                $quoteItem->setGocustomizerData($gocustomizer_data);

                if (empty($itemId)) {
                    $cart->getQuote()->addItem($quoteItem);
                }

                if(!empty($crosssells)) {
                    $crosssells = json_decode($crosssells,true);

                    foreach ($crosssells as $prod_id => $prod_qty) {
                        $crosssell = $this->catalogProductFactory->create()->setStoreId($this->storeManager->getStore()->getId())->load($prod_id);

                        // RM: Can this product be added to cart?
                        if ($crosssell->isSalable() == 1) {
                            $quoteItem = $cart->getQuote()->addProduct($crosssell, $this->dataObjectFactory->create(array('qty' => $prod_qty)));
                            $cart->getQuote()->addItem($quoteItem);
                        }
                    }
                }

                $cart->getQuote()->setTotalsCollectedFlag(false);
                $cart->save();
//                $this->_redirect('checkout/cart');
                return $result->setData($this->_url->getUrl('checkout/cart'));
//                return;
            } else {
                 $this->messageManager->addError(__('No product found'));
                 die('no productr found....');
//                $this->_redirect('checkout/cart');
                return $result->setData($this->_url->getUrl('checkout/cart'));
            }
        } catch (\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
//            $this->_redirect('checkout/cart');
//            die('Errrrrorrrr:'.$e->getMessage(). var_dump($e->getTraceAsString()));
            return $result->setData($this->_url->getUrl('checkout/cart'));
//            return;
        }
//        $this->_redirect('checkout/cart');
        return $result->setData($this->_url->getUrl('checkout/cart'));
//        return;
    }

}
