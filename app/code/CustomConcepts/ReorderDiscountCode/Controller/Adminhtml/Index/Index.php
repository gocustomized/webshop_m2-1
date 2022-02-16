<?php

/**
 * CustomConcepts_ReorderDiscountCode extension
 * @category  CustomConcepts_Extensions
 * @package   CustomConcepts_ReorderDiscountCode
 * @copyright Copyright (c) 2017
 * @author GoCustomized <info@gocustomized.com>
 */

namespace CustomConcepts\ReorderDiscountCode\Controller\Adminhtml\Index;

use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;

/**
 * Class for process send coupon code
 */
class Index extends \Magento\Backend\App\Action {

    const XML_PATH_DISCOUNTCOUPON_PREFIX = 'reorderdiscountcode/reorderdis_email/discountCoupanPrefix';
    const XML_PATH_MAX_COUPAN_NO = 'reorderdiscountcode/reorderdis_email/max_coupan';
    const XML_PATH_USESPERCOUPAN = 'reorderdiscountcode/reorderdis_email/usesPerCoupan';
    const XML_PATH_USESPERCUSTOMER = 'reorderdiscountcode/reorderdis_email/usesPerCustomer';
    const XML_PATH_CUSTOMERGROUP = 'reorderdiscountcode/reorderdis_email/customerGroup';
    const XML_PATH_CARTACTION = 'reorderdiscountcode/reorderdis_email/simpleAction';
    const XML_PATH_SENDER_EMAIL_IDENTTIY = 'reorderdiscountcode/reorderdis_email/reorderdis_sender_email_identity';

    /**
     * @var \CustomConcepts\ReorderDiscountCode\Helper\Data
     */
    protected $reorderdiscountcodeHelper;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     *
     * @var \Magento\Sales\Model\Order
     */
    protected $order;

    /**
     *
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $salesOrderFactory;

    /**
     *
     * @var \Magento\SalesRule\Model\RuleFactory
     */
    protected $salesRuleCouponFactory;

    /**
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     *
     * @var \Magento\Store\Model\StoreFactory
     */
    protected $storeFactory;

    /**
     *
     * @var \Magento\SalesRule\Model\RuleFactory
     */
    protected $salesRuleFactory;

    /**
     *
     * @var \Magento\Framework\TranslateInterface
     */
    protected $translateInterface;

    /**
     *
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $catalogProductFactory;

    /**
     *
     * @var \Magento\Email\Model\TemplateFactory
     */
    protected $emailTemplateFactory;

    /**
     *
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;

    /**
     *
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $_inlineTranslation;

    /**
     *
     * @var \Magento\Backend\Model\Auth
     */
    protected $authUser;

    /**
     *
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     *
     * @var \Magento\Directory\Model\CurrencyFactory
     */
    protected $currencyFactory;

    /**
     *
     * @var type String
     */
    protected $couponcode;

    /**
     *
     * @var type String
     */
    protected $discountCouponName;

    /**
     *
     * @var type Integer
     */
    protected $orderId;

    /**
     *
     * @var type Integer
     */
    protected $discount_total;

    /**
     *
     * @var type Integer
     */
    protected $templateId;

    /**
     *
     * @var type String
     */
    protected $note;

    /**
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Sales\Model\OrderFactory $salesOrderFactory
     * @param \Magento\SalesRule\Model\CouponFactory $salesRuleCouponFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreFactory $storeFactory
     * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory
     * @param \Magento\SalesRule\Model\RuleFactory $salesRuleFactory
     * @param \Magento\Framework\TranslateInterface $translateInterface
     * @param \Magento\Catalog\Model\ProductFactory $catalogProductFactory
     * @param \Magento\Email\Model\TemplateFactory $emailTemplateFactory
     * @param TransportBuilder $transportBuilder
     * @param StateInterface $inlineTranslation
     */
    public function __construct(
    \Magento\Backend\App\Action\Context $context, \Magento\Framework\App\Request\Http $request, \Magento\Sales\Model\OrderFactory $salesOrderFactory, \Magento\SalesRule\Model\CouponFactory $salesRuleCouponFactory, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Magento\Store\Model\StoreFactory $storeFactory, \Magento\Directory\Model\CurrencyFactory $currencyFactory, \Magento\SalesRule\Model\RuleFactory $salesRuleFactory, \Magento\Framework\TranslateInterface $translateInterface, \Magento\Catalog\Model\ProductFactory $catalogProductFactory, \Magento\Email\Model\TemplateFactory $emailTemplateFactory, TransportBuilder $transportBuilder, StateInterface $inlineTranslation
    ) {

        // $this->reorderdiscountcodeHelper = $reorderdiscountcodeHelper;
        $this->messageManager = $context->getMessageManager();
        $this->salesOrderFactory = $salesOrderFactory;
        $this->request = $request;
        $this->salesRuleCouponFactory = $salesRuleCouponFactory;
        $this->scopeConfig = $scopeConfig;
        $this->storeFactory = $storeFactory;
        $this->salesRuleFactory = $salesRuleFactory;
        $this->translateInterface = $translateInterface;
        $this->catalogProductFactory = $catalogProductFactory;
        $this->emailTemplateFactory = $emailTemplateFactory;
        $this->_transportBuilder = $transportBuilder;
        $this->_inlineTranslation = $inlineTranslation;
        $this->authUser = $context->getAuth()->getUser();
        $this->currencyFactory = $currencyFactory;

        parent::__construct($context);
    }

    /**
     * Check ACL is action allowed
     *
     * @return bool
     */
    protected function _isAllowed() {
        return $this->_authorization->isAllowed('CustomConcepts_ReorderDiscountCode::CustomConcepts_ReorderDiscountCode::config_customconcepts_reorderdiscountcode');
    }

    /**
     * Main Processing
     */
    public function execute() {
        try {
            $this->orderId = $this->getRequest()->getPost('order_id');
            $this->discount_total = $this->getRequest()->getPost('discount_total');
            $this->templateId = $this->getRequest()->getPost('template_id');
            $this->note = $this->getRequest()->getPost('note');
            $this->order = $this->getOrder();
            $this->discountCouponName = $this->getDiscountCouponName();

            $this->createCoupan();
            $this->sendMail();
        } catch (\Exception $e) {
            echo json_encode(['error' => __($e->getMessage())]);
        }
    }
    /**
     * Get the Order based on the Order ID passed
     * @return type
     */
    public function getOrder() {
        $order = $this->salesOrderFactory->create()->loadByIncrementId($this->orderId);
        return $order;
    }
    /**
     * Get coupon name
     * @return string
     */
    public function getDiscountCouponName() {

        $discountCouponPrefix = $this->scopeConfig->getValue(self::XML_PATH_DISCOUNTCOUPON_PREFIX, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, 0);
        $maxCouponNumber = $this->scopeConfig->getValue(self::XML_PATH_MAX_COUPAN_NO, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, 0);

        $discount_Coupon_name = $discountCouponPrefix . $this->orderId;

        $rule_exist = 0;

        $rule_exist = $this->salesRuleCouponFactory->create()->loadByCode($discount_Coupon_name)->getId();

        if ($rule_exist > 0) {
            for ($i = 1; $i < $maxCouponNumber; $i++) {
                $discount_Coupon_name = $discountCouponPrefix . $this->orderId . '-' . $i;
                $rule_exist_id = 0;
                $rule_exist_id = $this->salesRuleCouponFactory->create()->loadByCode($discount_Coupon_name)->getId();
                if ($rule_exist_id == 0 || $rule_exist_id == '') {
                    break;
                }
            }
        }

        return $discount_Coupon_name;
    }
    /**
     * Generate a coupon for specific user to sent
     */
    public function createCoupan() {

        $usesPerCoupon = $this->scopeConfig->getValue(self::XML_PATH_USESPERCOUPAN, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, 0);
        $usesPerCustomer = $this->scopeConfig->getValue(self::XML_PATH_USESPERCUSTOMER, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, 0);
        $customerGroup = $this->scopeConfig->getValue(self::XML_PATH_CUSTOMERGROUP, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, 0);
        $cartAction = $this->scopeConfig->getValue(self::XML_PATH_CARTACTION, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, 0);

        $storeId = $this->order->getStoreId();

        $websiteId = $this->storeFactory->create()->load($storeId)->getWebsiteId();

        $today = date("Y-m-d");

        $coupon_rule = $this->salesRuleFactory->create();

        $coupon_rule->setName($this->discountCouponName)
                ->setDescription($this->discountCouponName)
                ->setFromDate($today)
                ->setToDate()
                ->setCouponType(2)
                ->setCouponCode($this->discountCouponName)
                ->setUsesPerCoupon($usesPerCoupon)
                ->setUsesPerCustomer($usesPerCustomer)
                ->setCustomerGroupIds($customerGroup) //an array of customer grou pids
                ->setIsActive(1)
                //serialized conditions.  the following examples are empty
                ->setConditionsSerialized('')
                ->setActionsSerialized('')
                ->setStopRulesProcessing(0)
                ->setIsAdvanced(1)
                ->setProductIds('')
                ->setSortOrder(0)
                ->setSimpleAction($cartAction)
                ->setDiscountAmount($this->discount_total)
                ->setDiscountQty(null)
                ->setDiscountStep('0')
                ->setSimpleFreeShipping('1')
                ->setApplyToShipping('0')
                ->setIsRss(1)
                ->setCreatedBy($this->authUser->getUsername())
                ->setWebsiteIds(array($websiteId));
        $coupon_rule->save();

        $this->couponcode = $coupon_rule->getCouponCode();
        //$this->logger->log(null, 'rule save 1');

        $rule = $this->salesRuleCouponFactory->create()->loadByCode($this->discountCouponName);
        $rule->setData('store_id', $storeId);
        $rule->save();
    }
    /**
     * Once coupon generated sent mail to user
     */
    public function sendMail() {

        $customer_name = $this->order->getCustomerName();
        $store_group = $this->order->getStoreGroupName();
        $couponcode = $this->couponcode;
        $email_id = $this->order->getCustomerEmail();
        $storeId = $this->order->getStoreId();

        $emailTemplateVariables = [];
        $emailTemplateVariables['increment_id'] = $this->orderId;
        $emailTemplateVariables['CustomerName'] = $customer_name;
        $emailTemplateVariables['StoreGroupName'] = $store_group;
        $emailTemplateVariables['discountcode'] = $couponcode;
        $emailTemplateVariables['note'] = $this->note;
        $store = $this->storeFactory->create()->load($storeId);
        $currency_code = $store->getCurrentCurrencyCode();
        $currency = $this->currencyFactory->create()->load($currency_code);
        $currencySymbol = $currency->getCurrencySymbol();
        $emailTemplateVariables['discountvalue'] = $currencySymbol . $this->discount_total;

        $marchentNotificationMailId = $this->scopeConfig->getValue(self::XML_PATH_SENDER_EMAIL_IDENTTIY, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        $senderMailId = $this->scopeConfig->getValue("trans_email/ident_$marchentNotificationMailId/email", \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        $senderName = $this->scopeConfig->getValue("trans_email/ident_$marchentNotificationMailId/name", \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);

        $emailTemplate = $this->emailTemplate();

        if ($this->templateId) {
            $sender = Array('name' => $senderName,
                'email' => $senderMailId);
            $this->_inlineTranslation->suspend();
            $transport = $this->_transportBuilder
                    ->setTemplateIdentifier($this->templateId)
                    ->setTemplateOptions([
                        'area' => \Magento\Framework\App\Area::AREA_ADMINHTML,
                        'store' => $storeId,
                    ])
                    ->setTemplateVars($emailTemplateVariables)
                    ->setFrom($sender)
                    ->addTo($email_id)
                    ->getTransport();

            $transport->sendMessage();
            $this->_inlineTranslation->resume();
            $orderData = $this->order->getData();
            $ordered_items = floor($orderData['total_qty_ordered']); //$order->getAllItems();
            $discount_commment_set = 0;

            if ($ordered_items == 1) {
                $this->setOrderStatus($this->order, $this->orderId, $couponcode);
                //$this->logger->log(null, 'order ID set to complete if Item is 1: '.$order_id);
                $discount_commment_set = 1;
                echo 'view';
            } else {
                echo 'ship';
            }

            if (!$this->order->canShip() && $discount_commment_set == 0) {
                $this->setOrderStatus($this->order, $this->orderId, $couponcode);
                //$this->logger->log(null, 'order ID set to complete if order cannot ship: '.$order_id);
            } elseif ($this->order->canShip() && $discount_commment_set == 0) {
                $this->setOrderStatus($this->order, $this->orderId, $couponcode);
                //$this->logger->log(null, 'order can ship: '.$order_id);
            }
        } else {
            $emailTemplate->setSenderName($senderName);     //mail sender name
            $emailTemplate->setSenderEmail($senderMailId);  //mail sender email id
            $emailTemplate->setTemplateSubject('Reorder discount coupon code');
            $emailTemplate->setDesignConfig(array('area' => 'frontend'));
            $processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables); //it return the temp body
            $store = $storeId;
            foreach ($sendTo as $recipient) {
                $emailTemplate->send($recipient['email'], $recipient['name'], $emailTemplateVariables);  //send mail to customer email ids
            }
        }
        $this->messageManager->addSuccess(__('Reorder mail sent to client with discount code: ' . $couponcode));
    }
    /**
     * Get default template
     * @return type
     */
    public function emailTemplate() {

        if ($this->templateId) {   //if it is user template then this process is continue
            $emailTemplate = $this->emailTemplateFactory->create()->load($this->templateId);
        } else {   //  we are calling default template
            $emailTemplate = $this->emailTemplateFactory->create()
                    ->loadDefault('reorderdiscountcode_reorderdis_email_email_template');
        }

        return $emailTemplate;
    }
    /**
     * Set the order status
     * @param type $order
     * @param type $order_id
     * @param type $couponcode
     * @param type $discountAmount
     * @param type $emailName
     */
    public function setOrderStatus($order, $order_id, $couponcode, $discountAmount = NULL, $emailName = false) {

        $items = $order->getAllItems();
        $order_total = $order->getGrandTotal();
        //NEED TO CHANGE THIS AS PER THE NEED CURRENTLY WE ARE USING DIFFERENT MODULE FOR DROPSHIPMENT
//        foreach ($items as $item) {
//            $product_id = $item->getProductId();
//            $product = $this->catalogProductFactory->create()->load($product_id);
//
//            $product_supplier_name = $product->getAttributeText('supplier');
//
//            if ($product_supplier_name != '') {
//                $supplier_id = 0;
//                $supplier_result = Mage::getModel('supplier/supplier')->getSupplierByName($product_supplier_name);
//                $supplier_id = $supplier_result['id'];
//
//                Mage::log('supplier id: ' . $supplier_id, null, 'reorder.log');
//                /*
//                  $selectobjects = $read->query("SELECT id FROM mage_supplier_users WHERE name = '$product_supplier_name'");
//                  while($row = $selectobjects->fetch())
//                  {
//                  $supplier_id = $row['id'];
//                  }
//                 */
//                if ($supplier_id > 0 && (int) $discountAmount >= (int) $order_total) {
//                    $order_number = $order_id;
//                    /*
//                      $write = Mage::getSingleton('core/resource')->getConnection('core_write');
//                      $write->query("UPDATE mage_supplier_dropship_items SET status= '5' WHERE supplier_id = '$supplier_id' AND order_number= $order_number");
//                     */
//                    $dropship_collection = Mage::getModel('supplier/dropshipitems')->getCollection()->addFieldToFilter('order_number', $order_number)->addFieldToFilter('supplier_id', $supplier_id);
//                    $dropship_item = $dropship_collection->getFirstItem();
//                    $dropship_item->setData('status', 5);
//                    $dropship_item->save();
//                }
//            }
//        }
        if ((int) $discountAmount >= (int) $order_total) {
            $order->setData('state', "complete");
            $order->setStatus("complete");
        }

        $history = $order->addStatusHistoryComment(($emailName? : 'Reorder mail') . ' sent to client with discount code: ' . $couponcode . ', By: ' . $this->authUser->getUsername(), false);
        $history->setIsCustomerNotified(false);
        $order->save();
    }

}
