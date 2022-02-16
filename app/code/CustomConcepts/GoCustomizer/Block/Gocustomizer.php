<?php

/**
 * CustomConcepts_GoCustomizer extension
 * @category  CustomConcepts_Extensions
 * @package   CustomConcepts_GoCustomizer
 * @copyright Copyright (c) 2017
 * @author GoCustomized <info@gocustomized.com>
 */

namespace CustomConcepts\GoCustomizer\Block;

class Gocustomizer extends \Magento\Framework\View\Element\Template {

    /**
     *
     * @var type \Magento\Framework\Registry
     */
    protected $registry;

    /**
     *
     * @var type String
     */
    protected $redirectUrl;

    /**
     *
     * @var type \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     *
     * @var type \Magento\Framework\App\ResourceConnection
     */
    protected $resourceConnection;

    /**
     *
     * @var type \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $catalogResourceModelProductCollectionFactory;

    /**
     * Scope config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     *
     * @var type $context->getSession()->getSessionId();
     */
    protected $sessionId;
    protected $goCustomizerProductId;
    protected $customizerProductType;
    protected $customizerSession = '';
    protected $request;
    protected $productFactory;
    protected $generic;
    protected $customerSession;
    protected $redirect;
    protected $configurableProductResourceModelProductTypeConfigurable;
    protected $checkoutCart;

    /**
     * Gocustomizer constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $catalogResourceModelProductCollectionFactory
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $catalogResourceModelProductCollectionFactory,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\Session\Generic $generic,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable $configurableProductResourceModelProductTypeConfigurable,
        \Magento\Checkout\Model\Cart $checkoutCart,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->storeManager = $context->getStoreManager();
        $this->resourceConnection = $resourceConnection;
        $this->sessionId = $context->getSession()->getSessionId();
        $this->catalogResourceModelProductCollectionFactory = $catalogResourceModelProductCollectionFactory;
        $this->scopeConfig = $context->getScopeConfig();
        $this->request = $request;
        $this->productFactory = $productFactory;
        $this->generic = $generic;
        $this->customerSession = $customerSession;
        $this->redirect = $redirect;
        $this->configurableProductResourceModelProductTypeConfigurable = $configurableProductResourceModelProductTypeConfigurable;
        $this->checkoutCart = $checkoutCart;
        parent::__construct(
                $context, $data
        );
    }

    public function _prepareLayout() {
        $params = $this->request->getParams();
        $this->setData('customizer_options', $params);
        return parent::_prepareLayout();
    }

    /**
     * Setup customizer ID for a product
     * @param type $customizerProductId
     */
    public function setGoCustomizerProductId($customizerProductId) {
        $this->goCustomizerProductId = $customizerProductId;
    }

    /**
     * Get the customizer Id for a product
     * @param type $product
     * @return boolean
     */
    public function getGoCustomizerProductId($product = false) {
        if ($this->goCustomizerProductId) {
            return $this->goCustomizerProductId;
        }
        if (!$product) {
            $product = $this->registry->registry('current_product');
        }
        if ($product) {
            return $product->getData('gocustomizer_product_id');
        }
        return false;
    }

    /**
     * Set a customizer session
     * @param type $session
     */
    public function setCustomizerSession($session) {
        $this->customizerSession = $session;
    }

    /**
     * Return a customizer session
     * @return type
     */
    public function getCustomizerSession() {
        return $this->customizerSession;
    }

    /**
     * Set redirection URL in customizer page
     * @param type $url
     */
    public function setRedirectUrl($url = "") {
        $this->redirectUrl = $url;
    }

    /**
     * Get redirection URL in customizer page
     * @return type
     */
    public function getRedirectUrl() {
        return $this->redirectUrl;
    }

    /**
     * Get the customizer product informtaion for prices_sku
     * @param type $store_code
     * @return type
     */
    public function getGocustomizerProductInfo($store_code = false) {
        if ($store_code) {
            $storeview_id = $this->storeManager->getStore($store_code)->getId();
        } else {
            $storeview_id = $this->storeManager->getStore()->getStoreId();
        }
        $resource = $this->resourceConnection;
        $readConnection = $resource->getConnection('core_read');

        $po_type_value = $resource->getTableName('catalog_product_option_type_value');
        $product_option_table = $resource->getTableName('catalog_product_option');

        $query = "SELECT LEFT($po_type_value.sku,length($po_type_value.sku)-2) as partial_sku, $product_option_table.product_id FROM $po_type_value INNER JOIN $product_option_table on $po_type_value.option_id = $product_option_table.option_id WHERE $po_type_value.sku IS NOT NULL GROUP BY product_id";

        $results = $readConnection->fetchAssoc($query);
        foreach ($results as &$result) {
            $product = $this->catalogResourceModelProductCollectionFactory->create()->addStoreFilter($storeview_id)->addFieldToFilter('entity_id', $result['product_id'])->addAttributeToSelect('stock_status')->addAttributeToSelect('price')->getFirstItem();
            $product->setStoreId($storeview_id);
            $result['shipment'] = __($product->getAttributeText('stock_status'));
            //$result['price'] = Mage::helper('core')->currency($product->getPrice(), true, false);
            unset($result['partial_sku']);
            unset($result['product_id']);
        }

        return json_encode($results);
    }

    /**
     * Return Store code
     * @return type
     */
    public function getStoreCode() {
        return $this->storeManager->getStore()->getCode();
    }

    /**
     * Get the session ID
     * @return type
     */
    public function getSessionId() {
        return $this->sessionId;
    }

    /**
     * Get configuration value
     * @param string $path
     * @return type mixed
     */
    public function getConfigValue($path) {
        return $this->scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getHomeUrl(){
        return $this->storeManager->getStore()->getBaseUrl();
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCustomizerProductType($product = false){
        $customizerProdId = $this->getGoCustomizerProductId($product);
        if(!$this->customizerProductType && $customizerProdId){
            if(!$product){
                $product = $this->productFactory->create()->loadByAttribute('gocustomizer_product_id', $customizerProdId);
            }
            $this->customizerProductType = $product->getAttributeText('customizerproduct_type');
        }
        return $this->customizerProductType;
    }

    public function initializeBlock(){
        $product_id             = $this->getRequest()->getParam('product_id');
        $itemId                 = (int) $this->getRequest()->getParam('item_id');

        if($product_id && $itemId){ //EDIT PRODUCT
            /** Get Parent configurable product */
            $parentIds              = $this->configurableProductResourceModelProductTypeConfigurable->getParentIdsByChild($product_id);
            $configurableProduct    = $this->productFactory->create()->load($parentIds[0]);
            /**End Get Parent configurable product*/

            $customizer_product_id  = (int) $configurableProduct->getData('gocustomizer_product_id');

            $quoteItem = $this->checkoutCart->getQuote()->getItemById($itemId);
            $gocustomizerdata = unserialize($quoteItem->getGocustomizerData());

            /**Set Item Id to session for AddToCartController */
            $this->generic->setItemId($itemId);

            $this->setCustomerAuth($gocustomizerdata['customer_auth']);
            $this->setCustomizerSession($gocustomizerdata['session']);
            $this->setCustomizerAction('edit');
            $this->setGoCustomizerProductId($customizer_product_id);
        } else { //ADD PRODUCT
            $this->setCustomerAuth($this->customerSession->getSessionId());
            $this->setCustomizerSession(md5(uniqid()));
            $this->setCustomizerAction('init');
            $this->setGoCustomizerProductId($this->getRequest()->getParam('customizable'));

            $this->generic->unsetData('item_id');
        }

        $this->setRedirectUrl($this->redirect->getRefererUrl());

        return $this;
    }
}
