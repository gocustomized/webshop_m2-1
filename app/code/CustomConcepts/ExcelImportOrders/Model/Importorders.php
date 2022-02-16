<?php

/**
 * CustomConcepts_ExcelImportOrders extension
 * @category  CustomConcepts_Extensions
 * @package   CustomConcepts_ExcelImportOrders
 * @copyright Copyright (c) 2017
 * @author GoCustomized <info@gocustomized.com>
 */

namespace CustomConcepts\ExcelImportOrders\Model;

class Importorders extends \Magento\Framework\Model\AbstractModel {

    /**
     *
     * @var type int
     */
    public $excel_serial_no = 0;

    /**
     *
     * @var type Object
     */
    public $quote_object = '';

    /**
     *
     * @var type boolean
     */
    public $success = 0;

    /**
     *
     * @var type int
     */
    public $order_fields = 0;

    /**
     * @var \Magento\Quote\Model\QuoteFactory
     */
    protected $quoteQuoteFactory;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerCustomerFactory;

    /**
     * @var \Magento\Customer\Model\AddressFactory
     */
    protected $customerAddressFactory;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $salesOrderFactory;

    /**
     * @var \Magento\Framework\DB\TransactionFactory
     */
    protected $transactionFactory;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $catalogProductFactory;

    /**
     * @var \Magento\Eav\Model\Entity\Attribute\SetFactory
     */
    protected $eavEntityAttributeSetFactory;

    /**
     * @var \Magento\Framework\Session\GenericFactory
     */
    protected $genericFactory;

    /**
     * @var \Magento\Quote\Model\QuoteManagement
     */
    protected $quoteManagment;

    /**
     * @var \Magento\Framework\DataObjectFactory
     */
    protected $dataObjectFactory;

    /**
     *
     * @var type \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     *
     * @var type  \Magento\Framework\App\Filesystem\DirectoryList
     */
    protected $directoryList;

    /**
     *
     * @var type \Magento\Framework\UrlInterface
     */
    protected $urlInterface;

    /**
     *
     * @var type array
     */
    protected $headerKeys;
    /**
     * 
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Quote\Model\QuoteFactory $quoteQuoteFactory
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerCustomerFactory
     * @param \Magento\Customer\Model\AddressFactory $customerAddressFactory
     * @param \Magento\Sales\Model\OrderFactory $salesOrderFactory
     * @param \Magento\Framework\DB\TransactionFactory $transactionFactory
     * @param \Magento\Catalog\Model\ProductFactory $catalogProductFactory
     * @param \Magento\Eav\Model\Entity\Attribute\SetFactory $eavEntityAttributeSetFactory
     * @param \Magento\Framework\Session\GenericFactory $genericFactory
     * @param \Magento\Framework\DataObjectFactory $dataObjectFactory
     * @param \Magento\Sales\Model\Service\InvoiceService $invoiceService
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directoryList
     * @param \Magento\Framework\UrlInterface $urlInterface
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
            \Magento\Framework\Model\Context $context, 
            \Magento\Framework\Registry $registry, 
            \Magento\Quote\Model\QuoteFactory $quoteQuoteFactory,
            \Magento\Customer\Api\CustomerRepositoryInterface $customerCustomerFactory, 
            \Magento\Customer\Model\AddressFactory $customerAddressFactory, 
            \Magento\Sales\Model\OrderFactory $salesOrderFactory, 
            \Magento\Framework\DB\TransactionFactory $transactionFactory, 
            \Magento\Catalog\Model\ProductFactory $catalogProductFactory, 
            \Magento\Eav\Model\Entity\Attribute\SetFactory $eavEntityAttributeSetFactory,
            \Magento\Framework\Session\GenericFactory $genericFactory,
            \Magento\Framework\DataObjectFactory $dataObjectFactory, 
            \Magento\Sales\Model\Service\InvoiceService $invoiceService, 
            \Magento\Framework\Message\ManagerInterface $messageManager,
            \Magento\Framework\App\Filesystem\DirectoryList $directoryList, 
            \Magento\Framework\UrlInterface $urlInterface, 
            \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null, 
            \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null, 
            array $data = []
    ) {
        
        $this->dataObjectFactory = $dataObjectFactory;
        $this->quoteQuoteFactory = $quoteQuoteFactory;
        $this->customerCustomerFactory = $customerCustomerFactory;
        $this->customerAddressFactory = $customerAddressFactory;
        $this->salesOrderFactory = $salesOrderFactory;
        $this->transactionFactory = $transactionFactory;
        $this->catalogProductFactory = $catalogProductFactory;
        $this->eavEntityAttributeSetFactory = $eavEntityAttributeSetFactory;
        $this->genericFactory = $genericFactory;
        $this->invoiceService = $invoiceService;
        $this->messageManager = $messageManager;
        $this->directoryList = $directoryList;
        $this->urlInterface = $urlInterface;
        
        parent::__construct( $context, $registry, $resource, $resourceCollection, $data );
    }

    public function readExcel($excelFile) {
        require_once 'excel_reader2.php';
        $data = new \Spreadsheet_Excel_Reader($excelFile);

        for ($i = 0; $i < count($data->sheets); $i++) { // Loop to get all sheets in a file.
            if (count($data->sheets[$i]['cells']) > 0) { // checking sheet not empty
                $total_rows = count($data->sheets[$i]['cells']) - 1;
                $k = 1;
                $header =$data->sheets[$i]['cells'][1];
                $this->setHeaderKeys($header); // set Header keys , reverse key and value
                for ($j = 2; $j <= count($data->sheets[$i]['cells']); $j++) { // loop used to get each row of the sheet
                    $serial_no = $data->sheets[$i]['cells'][$j][$this->headerKeys['serial_no']];
                    $orders_data = $data->sheets[$i]['cells'][$j];

                    if ($this->excel_serial_no == 0 && $serial_no != '') {
                        $this->addDataToQuote($serial_no, $orders_data);
                    } elseif ($this->excel_serial_no != $serial_no && $this->excel_serial_no > 0 && $serial_no != '') {
                        $this->createOrder($this->quote_object);
                        $this->addDataToQuote($serial_no, $orders_data);
                    } elseif ($serial_no != '') {
                        $this->insertOrderItemData($orders_data, $this->quote_object);
                    }
                    if ($k == $total_rows) {
                        $this->createOrder($this->quote_object);
                    }
                    $k++;
                }
            }
        }
        if ($this->success) {
            $this->messageManager->addSuccess(__('Total ' . $this->success . ' order(s) imported successfully!'));
        }
    }

    /**
     * // reverse key and value and for header keys
     * @param type $header
     */
    public function setHeaderKeys($header){
        foreach($header as $key=>$value){
            $this->headerKeys[$value] = trim(strtolower($key));
        }
    }

    /**
     * @param type $serial_no
     * @param type $orders_data
     */
    public function addDataToQuote($serial_no,$orders_data){
        $this->excel_serial_no = $serial_no;
        $quote = $this->insertOrderData($orders_data);
        $this->insertOrderItemData($orders_data, $quote);
    }

    /**
     * 
     * @param type $orders_data
     * @return type $quote
     */
    public function insertOrderData($orders_data) {
        try {
            $headerkeys = $this->headerKeys;
            $store_id = $orders_data[$headerkeys['store_id']];
           
            $customer_id = isset($orders_data[$headerkeys['customer_id']]) ? $orders_data[$headerkeys['customer_id']] : '';

            $quote = $this->quoteQuoteFactory->create()->setStoreId($store_id);
            
            $customer = $this->customerCustomerFactory->getById($customer_id);
            $quote->assignCustomer($customer);
            $billingAddressId = $customer->getDefaultBilling();
            
            $shipping_addr = $this->getShippingAddress($orders_data);
            
            if ($billingAddressId) {
                $billing_address = $this->getBillingAddressFromBillingId($billingAddressId);
            } else {
                $billing_address = $shipping_addr;
                $billing_address['is_default_billing'] = 1;
                $billing_address['mode'] = 'billing';
            }

            $quote->getBillingAddress()->addData($billing_address);
            $quote->getShippingAddress()->addData($shipping_addr);

            $coupon_code = isset($orders_data[$headerkeys['coupon_code']]) ? $orders_data[$headerkeys['coupon_code']] : '';
            $po_number = isset($orders_data[$headerkeys['po_number']]) ? $orders_data[$headerkeys['po_number']] : '';
            $shipping_method_id = isset($orders_data[$headerkeys['shipment_method_carrier_id']]) ? $orders_data[$headerkeys['shipment_method_carrier_id']] : '';
            $extra_reference = isset($orders_data[$headerkeys['extra_ref']]) ? $orders_data[$headerkeys['extra_ref']] : '';

            $this->order_fields = $extra_reference . '|' . $po_number . '|' . $shipping_method_id . '|' . $coupon_code;

            unset($this->quote_object);
            $this->quote_object = $quote;
            return $quote;
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage() . ' for serial no: ' . $this->excel_serial_no);
        }
    }
    
    public function getShippingAddress($orders_data){
        $headerKeys = $this->headerKeys;
        $ship_address = isset($orders_data[$headerKeys['shipment_address']]) ? utf8_encode($orders_data[$headerKeys['shipment_address']]) : '';
        $ship_house = isset($orders_data[$headerKeys['shipment_housnr']]) ? utf8_encode($orders_data[$headerKeys['shipment_housnr']]) : '';
        $ship_addition = isset($orders_data[$headerKeys['shipment_addition']]) ? utf8_encode($orders_data[$headerKeys['shipment_addition']]) : '';

        $shipping_addr = array(
            "mode" => "shipping",
            "firstname" => isset($orders_data[$headerKeys['shipment_first_name']]) ? utf8_encode($orders_data[$headerKeys['shipment_first_name']]) : '',
            "lastname" => isset($orders_data[$headerKeys['shipment_last_name']]) ? utf8_encode($orders_data[$headerKeys['shipment_last_name']]) : '',
            "company" => isset($orders_data[$headerKeys['company']]) ? utf8_encode($orders_data[$headerKeys['company']]) : '',
            "street" => array('street[0]' => $ship_address, 'street[1]' => $ship_house, 'street[2]' => $ship_addition),
            "city" => isset($orders_data[$headerKeys['shipment_city']]) ? utf8_encode($orders_data[$headerKeys['shipment_city']]) : '',
            "region" => isset($orders_data[$headerKeys['shipment_state_province']]) ? $orders_data[$headerKeys['shipment_state_province']] : '',
            "postcode" => isset($orders_data[$headerKeys['zipcode']]) ? $orders_data[$headerKeys['zipcode']] : '',
            "country_id" => isset($orders_data[$headerKeys['shipment_country_code']]) ? $orders_data[$headerKeys['shipment_country_code']] : '',
            "telephone" => isset($orders_data[$headerKeys['phone']]) ? $orders_data[$headerKeys['phone']] : '',
            "fax" => "",
            "is_default_shipping" => 0,
            "is_default_billing" => 0
        );
        return $shipping_addr;
    }
    
    
    public function getBillingAddressFromBillingId($billingAddressId){
        $customer_address = $this->customerAddressFactory->create()->load($billingAddressId);
                
        $billing_address = array(
            "mode" => "billing",
            "firstname" => $customer_address->getData("firstname"),
            "lastname" => $customer_address->getData("lastname"),
            "company" => $customer_address->getCompany(),
            "street" => $customer_address->getStreet(), 
            "city" => $customer_address->getCity(),
            "region" => "",
            "postcode" => $customer_address->getData("postcode"),
            "country_id" => $customer_address->getCountry(),
            "telephone" => $customer_address->getTelephone(),
            "is_default_shipping" => 0,
            "is_default_billing" => 1
        );
        return $billing_address;
    }

    /**
     * 
     * @param type $quote
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createOrder($quote) {
        try {
            if (count($quote->getAllItems()) > 0) {
                $explode_fields = explode('|', $this->order_fields);
                $shipping_method_id = $explode_fields[2];
                $po_number = $explode_fields[1];
                $extra_reference = $explode_fields[0];
                $couponCode = $explode_fields[3];
                //// set discount code manually ////
                if ($couponCode) {
                    $quote->getShippingAddress()->setCollectShippingRates(true);
                    $quote->setCouponCode(strlen($couponCode) ? $couponCode : '');
                }

                // end discount code add manually
                $quote->getShippingAddress()
                        ->setShippingMethod('tablerate_bestway_' . $shipping_method_id)
                        ->setCollectShippingRates(true);
                $quote->collectTotals();

                // Set Sales Order Payment
                $quote->getPayment()->setQuote($quote)->importData(array('method' => 'purchaseorder', 'po_number' => $po_number));
                
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $this->quoteManagment = $objectManager->get('Magento\Quote\Model\QuoteManagement');

                $order = $this->quoteManagment->submit($quote);

                $increment_id = $order->getRealOrderId();

                if ($increment_id) {
                    $order = $this->salesOrderFactory->create()->loadByIncrementId($increment_id);
                    $order->setData("extra_reference", $extra_reference);
                    $order->setState('processing', true);
                    $order->setStatus('processing');
                    $order->save();
                    ////// create invoice automatically
                    $this->generateInvoice($order);
                    $this->success++;
                }
            } else {
                $this->messageManager->addError("Product couldn't added to order. make sure color option or other values are correct for serial no: " . $this->excel_serial_no);
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage() . ' for serial no: ' . $this->excel_serial_no);
        }
    }
    
    public function generateInvoice($order){
        if ($order->canInvoice()) {
            $invoice = $this->invoiceService->prepareInvoice($order);
            if (!$invoice->getTotalQty()) {
                throw new \Magento\Framework\Exception\LocalizedException(__('Cannot create an invoice without products.'));
            }
            $invoice->setRequestedCaptureCase(\Magento\Sales\Model\Order\Invoice::CAPTURE_ONLINE);
            $invoice->addComment('Invoice genrated automatically');
            $invoice->register();
            $transactionSave = $this->transactionFactory->create()
                    ->addObject($invoice)
                    ->addObject($order);
            $transactionSave->save();
        }
    }

    /**
     * 
     * @param type $orders_data
     * @param type $quote
     */
    public function insertOrderItemData($orders_data, $quote) {
        $headerKeys = $this->headerKeys;
        try {
            $store_id = isset($orders_data[$headerKeys['store_id']]) ? $orders_data[$headerKeys['store_id']] : '';
            $product_id = isset($orders_data[$headerKeys['product_id']]) ? $orders_data[$headerKeys['product_id']] : '';
            $color = isset($orders_data[$headerKeys['color']]) ? utf8_encode($orders_data[$headerKeys['color']]) : '';
            $quantity = isset($orders_data[$headerKeys['quantity']]) ? $orders_data[$headerKeys['quantity']] : '';
            $price = isset($orders_data[$headerKeys['price']]) ? $orders_data[$headerKeys['price']] : '';
            $image_link = isset($orders_data[$headerKeys['image']]) ? $orders_data[$headerKeys['image']] : '';
            $customer_id = isset($orders_data[$headerKeys['customer_id']]) ? $orders_data[$headerKeys['customer_id']] : '';

            $_product = $this->catalogProductFactory->create()->setStoreId($store_id)->load($product_id);
            $textdata_dutch = '';
            $attributeSetName = '';
            $custom_option_color_id = 0;
            $attributeSetName = $this->eavEntityAttributeSetFactory->create()->load($_product->getAttributeSetId())->getAttributeSetName();

            if ($attributeSetName == 'Customizer Product') {
                foreach ($_product->getOptions() as $o) {
                    $option_title = $o->getTitle();
                    if (strtolower($option_title) == 'select color') {
                        $option_id = $o->getId();
                        $values = $o->getValues();
                        foreach ($values as $v) {
                            $custom_option_value = $v->getData();
                            $option_type_id = $custom_option_value['option_type_id'];
                            $option_id = $custom_option_value['option_id'];
                            $custom_option_title = $custom_option_value['title'];
                            if (strtolower($custom_option_title) == strtolower($color)) {
                                $custom_option_color_id = $option_type_id;
                                $textdata_dutch = $custom_option_value['default_title'];
                                break;
                            }
                        }
                    }
                }
                $session_id = $this->genericFactory->create()->getEncryptedSessionId() . $this->excel_serial_no . $customer_id;

                //$working_dir = Mage::getBaseDir('media') . DS . 'customer' . DS . 'upload_images' . DS . date('Ymd') . DS . $session_id;
                $working_dir = $this->directoryList->getPath('media') . DIRECTORY_SEPARATOR . 'customer' . DIRECTORY_SEPARATOR . 'upload_images' . DIRECTORY_SEPARATOR . date('Ymd') . DIRECTORY_SEPARATOR . $session_id;
                if (!file_exists($working_dir)) {
                    mkdir($working_dir, 0777);
                }

                $thumbnail_name = $this->generateNewFileName('thumbnail', $session_id);

                $final_name = $this->generateNewFileName('final', $session_id);
                //Get the file
                $final_content = file_get_contents($image_link);
                //Store in the filesystem.
                $fp = fopen($working_dir . '/' . $final_name, "w");
                fwrite($fp, $final_content);
                fclose($fp);
                //$base_url = Mage::getBaseUrl(\Magento\Store\Model\Store::URL_TYPE_MEDIA) . 'customer/upload_images/' . date('Ymd') . '/' . $session_id . '/';
                $base_url = $this->urlInterface->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]) . 'customer/upload_images/' . date('Ymd') . '/' . $session_id . '/';
                $thumbnail_png_path = $base_url . $thumbnail_name;

                $final_png_path = $base_url . $final_name;

                //$base_path = Mage::getBaseDir('media') . DS . 'customer' . DS . 'upload_images' . DS . date('Ymd') . DS . $session_id . '/' . $final_name;
                $base_path = $this->directoryList->getPath('media') . DIRECTORY_SEPARATOR . 'customer' . DIRECTORY_SEPARATOR . 'upload_images' . DIRECTORY_SEPARATOR . date('Ymd') . DIRECTORY_SEPARATOR . $session_id . DIRECTORY_SEPARATOR . $final_name;

                foreach ($_product->getOptions() as $o) {
                    $_options[$o->getTitle()] = $o->getId();
                }

                $quoteItem = $quote->addProduct($_product, $this->dataObjectFactory->create(array('qty' => $quantity,
                            'options' => array(
                                $_options['Select Color'] => $custom_option_color_id,
                                $_options['optie'] => $color,
                                $_options['final image'] => $final_png_path,
                                $_options['original backgroundImage'] => "",
                                //$_options['base_path'] => $base_path,
                                $_options['original textData'] => trim($textdata_dutch),
                                $_options['thumbnail'] => $thumbnail_png_path,
                                $_options['session_id'] => $session_id
                ))));

                if (is_object($quoteItem) && $price) {
                    $quoteItem->setCustomPrice($price);
                    $quoteItem->setOriginalCustomPrice($price);
                }
            } else {
                $quoteItem = $quote->addProduct($_product, $this->dataObjectFactory->create(array('qty' => $quantity)));

                if (is_object($quoteItem) && $price) {
                    $quoteItem->setCustomPrice($price);
                    $quoteItem->setOriginalCustomPrice($price);
                }
            }
            unset($this->quote_object);
            $this->quote_object = $quote;
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            if ($e->getMessage()) {
                $this->messageManager->addError($e->getMessage() . ' for serial no: ' . $this->excel_serial_no);
            }
        }
    }
    
    /**
     * 
     * @param type $image_filename
     * @param type $session_id
     * @return type
     */
    public function generateNewFileName($image_filename, $session_id) {
        $count = 0;

        for ($i = 1; $i < 50; $i++) {
            $filename = $image_filename . '_' . ($count + $i) . '.png';
            //$file_path = Mage::getBaseDir("media") . DS . 'customer' . DS . 'upload_images' . DS . date('Ymd') . DS . $session_id . DS . $filename;
            $file_path = $this->directoryList->getPath('media') . DIRECTORY_SEPARATOR . 'customer' . DIRECTORY_SEPARATOR . 'upload_images' . DIRECTORY_SEPARATOR . date('Ymd') . DIRECTORY_SEPARATOR . $session_id . DIRECTORY_SEPARATOR . $filename;
            if (!file_exists($file_path)) {
                return $image_filename . '_' . ($count + $i) . '.png';
                break;
            }
        }
    }
}