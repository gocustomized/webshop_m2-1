<?php
namespace CustomConcepts\Faqs\Block;

use Bluebirdday\TranssmartSmartConnect\Model\Booking\ProfileRepository;
use CustomConcepts\Faqs\Helper\Data;
use CustomConcepts\Faqs\Model\ProdfaqsTopicsFactory;
use CustomConcepts\Faqs\Model\ResourceModel\Prodfaqs\CollectionFactory as ProdFaqsCollectionFactory;
use CustomConcepts\Faqs\Model\ResourceModel\ProdfaqsTopics\CollectionFactory;
use CustomConcepts\Faqs\Model\ResourceModel\ProdfaqsTopicsFactory as ResourceProdfaqsTopicsFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Pricing\Helper\Data as DataAlias;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context as ContextAlias;
use Magento\Store\Model\ScopeInterface as ScopeInterfaceAlias;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Widget\Model\Template\Filter;
use Magento\OfflineShipping\Model\ResourceModel\Carrier\TablerateFactory;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateRequestFactory;

class Faq extends \Magento\Framework\View\Element\Template
{
    /**
     * @var CollectionFactory
     */
    protected $prodfaqsTopicCollection;
    /**
     * @var Session
     */
    protected $customerSession;
    /**
     * @var ProdFaqsCollectionFactory
     */
    protected $prodfaqsCollection;
    /**
     * @var Data
     */
    protected $prodfaqsHelper;
    /**
     * @var ProdfaqsTopicsFactory
     */
    protected $prodfaqsTopicsFactory;
    /**
     * @var ResourceProdfaqsTopicsFactory
     */
    protected $prodfaqsTopicsResource;
    /**
     * @var Filter
     */
    protected $templateProcessor;


    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var ProfileRepository
     */
    protected $profileRepo;
    /**
     * @var Magento\Framework\Pricing\Helper\Data|DataAlias
     */
    protected $pricingHelper;
    private $tablerateFactory;
    /**
     * @var RateRequest
     */
    private $rateRequest;
    /**
     * @var RateRequestFactory
     */
    private $rateRequestFactory;

    /**
     * Faq constructor.
     * @param Template\Context $context
     * @param CollectionFactory $prodfaqsTopicCollection
     * @param ProdFaqsCollectionFactory $prodfaqsCollection
     * @param ProdfaqsTopicsFactory $prodfaqsTopicsFactory
     * @param ResourceProdfaqsTopicsFactory $prodfaqsTopicsResource
     * @param Session $customerSession
     * @param Data $prodfaqsHelper
     * @param Filter $templateProcessor
     * @param ResourceConnection $resourceConnection
     * @param StoreManagerInterface $storeManager
     * @param ProfileRepository $profileRepository
     * @param DataAlias $pricingHelper
     * @param TablerateFactory $tablerateFactory
     * @param RateRequest $rateRequest
     * @param RateRequestFactory $rateRequestFactory
     * @param array $data
     */
    public function __construct(
        ContextAlias $context,
        CollectionFactory $prodfaqsTopicCollection,
        ProdFaqsCollectionFactory $prodfaqsCollection,
        ProdfaqsTopicsFactory $prodfaqsTopicsFactory,
        ResourceProdfaqsTopicsFactory $prodfaqsTopicsResource,
        Session $customerSession,
        Data $prodfaqsHelper,
        Filter $templateProcessor,


        ResourceConnection $resourceConnection,
        StoreManagerInterface $storeManager,
        ProfileRepository $profileRepository,
        DataAlias $pricingHelper,
        TablerateFactory $tablerateFactory,
        RateRequest $rateRequest,
        RateRequestFactory $rateRequestFactory,
        array $data = []
    ){
        $this->prodfaqsTopicCollection = $prodfaqsTopicCollection;
        $this->prodfaqsCollection = $prodfaqsCollection;
        $this->prodfaqsTopicsFactory = $prodfaqsTopicsFactory;
        $this->prodfaqsTopicsResource = $prodfaqsTopicsResource;
        $this->customerSession = $customerSession;
        $this->prodfaqsHelper = $prodfaqsHelper;
        $this->templateProcessor = $templateProcessor;
        $this->storeManager = $storeManager;
        $this->profileRepo = $profileRepository;
        $this->resourceConnection = $resourceConnection;
        $this->pricingHelper = $pricingHelper;
        $this->tablerateFactory = $tablerateFactory;
        $this->rateRequest = $rateRequest;
        $this->rateRequestFactory = $rateRequestFactory;

        parent::__construct($context, $data);

    }

    /**
     * @return Data
     */
    public function getProdfaqsHelper(){
        return $this->prodfaqsHelper;
    }

    /**
     * @return mixed
     */
    public function showShippingOnId(){
        return $this->prodfaqsHelper->showShippingOnId();
    }

    /**
     * @return mixed
     */
    public function isFaqSearchBlock(){
        return $this->_scopeConfig->getValue('prodfaqs/general/faq_search_block');
    }

    /**
     * @return mixed
     */
    public function getDisplayCategories(){
        return $this->prodfaqsHelper->getDisplayCategories();
    }

    /**
     * @return string
     */
    public function getFaqUrl() {
        return $this->getUrl($this->prodfaqsHelper->getListIdentifier());
    }

    /**
     * @return mixed
     */
    public function getProdfaqsCollection(){
        $sortBy = $this->prodfaqsHelper->getGeneralFaqSorting();
        switch ($sortBy){
            case 'helpful':
                $sortCond = 'main_table.rating_stars';
                $sortVal = 'DESC';
                break;
            case 'order':
                $sortCond = 'main_table.faq_order';
                $sortVal = 'ASC';
                break;
            default:
                $sortCond = 'main_table.created_at';
                $sortVal = 'DESC';
                break;
        }

        $collection = $this->prodfaqsCollection->create()
            ->addFieldToFilter('status',1)
            ->setOrder($sortCond,$sortVal);

        if($this->customerSession->isLoggedIn()){
            $collection->addFieldToFilter('main_table.visibility','public');
        }

        return $collection;
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getTopicsCollections(){
        $collection = $this->prodfaqsTopicCollection->create()
            ->addStoreFilter($this->_storeManager->getStore()->getId())
            ->addFieldToFilter('status',1)
            ->addOrder('main_table.topic_order', 'ASC');

        if($this->prodfaqsHelper->getDisplayCategories() == 'selected'){
            $collection->addFieldToFilter('show_on_main',1);
        }

        return $collection;
    }

    /**
     * @param $string
     * @return string
     */
    public function filterOutputHtml($string){
        return $this->templateProcessor->filter($string);
    }

    public function getHTMLShippingMethods()
    {

        $websiteId = $this->storeManager->getStore()->getWebsiteId();
        $request = $this->rateRequestFactory->create();
        $country_id = $this->_scopeConfig->getValue('general/country/default', ScopeInterfaceAlias::SCOPE_STORE);
        $request->setWebsiteId($websiteId);
        $request->setConditionName($this->storeManager->getWebsite($websiteId)->getConfig('carriers/tablerate/condition_name'));
        $request->setDestCountryId($country_id);
        $request->setPackageValue(0);
        $request->setPackageValueWithDiscount(0);
        $request->setPackageWeight(0);
        $request->setPackageQty(0);
        $rates = $this->tablerateFactory->create()->getRates($request);

        $descriptions = '<ul class="shipping-methods">';
        foreach ($rates as $row) {
            $carrierId = $row['transsmart_bookingprofile_id'];
            $bookingProfile = $this->profileRepo->load($carrierId);
            $carrier_code = $bookingProfile->getCode();
            $name = $this->_scopeConfig->getValue("transsmart_profiles/$carrier_code/title", ScopeInterfaceAlias::SCOPE_STORE);            ;
            $description = $this->_scopeConfig->getValue("transsmart_profiles/$carrier_code/textarea", ScopeInterfaceAlias::SCOPE_STORE);
            $description = $description?' ('.$description.') ':' ';
//            $name = $carrierprofile->getName();
            $descriptions .= '<li>'.$name . $description . $this->pricingHelper->currency(
                    $row['price'],
                    true,
                    false
                ) . '</li>';
        }
        $descriptions .= '</ul>';
        return $descriptions;
    }

    public function getShippingFaqId(){
        return $this->_scopeConfig->getValue('prodfaqs/general/faq_id_for_dynamic_shippinginfo', ScopeInterfaceAlias::SCOPE_STORE);
    }
}

