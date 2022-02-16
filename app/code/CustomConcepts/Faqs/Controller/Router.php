<?php
namespace CustomConcepts\Faqs\Controller;

class Router implements \Magento\Framework\App\RouterInterface
{
    /**
     * @var \Magento\Framework\App\ActionFactory
     */
    protected $actionFactory;
    /**
     * @var \Magento\Framework\App\ResponseInterface
     */
    protected $_response;
    /**
     * @var \CustomConcepts\Faqs\Helper\Data
     */
    protected $prodfaqsHelper;
    /**
     * @var \CustomConcepts\Faqs\Model\ResourceModel\ProdfaqsTopics
     */
    protected $prodfaqsTopicsResource;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;
    /**
     * @var \Magento\Catalog\Model\Product\Attribute\Source\Status
     */
    protected $productStatus;
    /**
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $productVisibility;

    /**
     * Router constructor.
     * @param \Magento\Framework\App\ActionFactory $actionFactory
     * @param \Magento\Framework\App\ResponseInterface $response
     * @param \CustomConcepts\Faqs\Helper\Data $prodfaqsHelper
     * @param \CustomConcepts\Faqs\Model\ResourceModel\ProdfaqsTopics $prodfaqsTopicsResource
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus
     * @param \Magento\Catalog\Model\Product\Visibility $productVisibility
     */
    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Magento\Framework\App\ResponseInterface $response,
        \CustomConcepts\Faqs\Helper\Data $prodfaqsHelper,
        \CustomConcepts\Faqs\Model\ResourceModel\ProdfaqsTopics $prodfaqsTopicsResource,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus,
        \Magento\Catalog\Model\Product\Visibility $productVisibility
    ) {
        $this->actionFactory = $actionFactory;
        $this->_response = $response;
        $this->prodfaqsHelper = $prodfaqsHelper;
        $this->prodfaqsTopicsResource = $prodfaqsTopicsResource;
        $this->storeManager = $storeManager;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->productStatus = $productStatus;
        $this->productVisibility = $productVisibility;
    }

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @return bool|\Magento\Framework\App\ActionInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function match(\Magento\Framework\App\RequestInterface $request)
    {
        $route = $this->prodfaqsHelper->getListIdentifier(); // = 'help'
        $identifier = trim($request->getPathInfo(), '/');
        $identifier = str_replace($this->prodfaqsHelper->getSeoUrlSuffix(), '', $identifier);

        if(strpos($identifier, $route) !== false) {
            $frontName = isset(explode('/', $identifier)[0]) ? explode('/', $identifier)[0] : '';
            $moduleName = $request->getModuleName() ?: $frontName;

            if($moduleName == 'faqs' || $moduleName == 'helpcenter'){ //check if its already redirected to prodfaqs
                return false;
            }
            $request->setRouteName($identifier); //to fix the error for amasty/module-gdpr. Amasty\Gdpr\Observer\Checkout\Compatibility

            if($identifier == $route){
                $request->setModuleName('faqs')
                    ->setControllerName('index')
                    ->setActionName('index');

            } elseif (strpos($identifier, $route . '/contactforms') === 0){
                $request->setModuleName('helpcenter')
                    ->setControllerName('contactform')
                    ->setActionName('index');

            } elseif(strpos($identifier, $route . '/search') === 0){
                $request->setModuleName('faqs')
                    ->setControllerName('index')
                    ->setActionName('search')
                    ->setParam('id', (int)$request->getParam('id'));

            } elseif ( strpos($identifier, $route) === 0 && strlen($identifier) > strlen($route) && strpos($identifier, '-questions') === false ) {
                $identifier = trim(substr($identifier, strlen($route)), '/');
                $topicId = $this->prodfaqsTopicsResource->checkIdentifier($identifier, $this->storeManager->getStore()->getId());

                $request->setModuleName('faqs')
                    ->setControllerName('index')
                    ->setActionName('view')
                    ->setParam('id', $topicId);

            } else {
                return false;
            }
        } else {
            return false;
        }
        return $this->actionFactory->create(
            'Magento\Framework\App\Action\Forward',
            ['request' => $request]
        );
    }
}
