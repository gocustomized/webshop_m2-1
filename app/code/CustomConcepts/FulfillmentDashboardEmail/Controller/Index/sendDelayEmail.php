<?php
namespace CustomConcepts\FulfillmentDashboardEmail\Controller\Index;

//use Magento\Framework\Exception\LocalizedException;

use Magento\Framework\App\Action\Context;
use \Magento\Store\Model\ScopeInterface;

class SendDelayEmail extends \Magento\Framework\App\Action\Action {

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $jsonResultFactory;
    /**
     * @var \CustomConcepts\Base\Helper\Logger
     */
    protected $logger;
    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $orderModelFactory;
    /**
     * @var \Magento\Sales\Model\ResourceModel\OrderFactory
     */
    protected $orderResourceModelFactory;
    /**
     * @var \CustomConcepts\Estimations\Helper\DeliveryDate
     */
    protected $deliveryDateHelper;
    /**
     * @var \Magento\Store\Api\StoreRepositoryInterface
     */
    protected $storeRepository;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $transportBuilder;
    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;
    /**
     * @var \CustomConcepts\Estimations\Api\EstimationDatesRepositoryInterface
     */
    protected $estimationDatesRepository;

    /**
     * SendDelayEmail constructor.
     * @param Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory
     * @param \Magento\Sales\Model\OrderFactory $orderModelFactory
     * @param \Magento\Sales\Model\ResourceModel\OrderFactory $orderResourceModelFactory
     * @param \CustomConcepts\Estimations\Helper\DeliveryDate $deliveryDateHelper
     * @param \Magento\Store\Api\StoreRepositoryInterface $storeRepository
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \CustomConcepts\Base\Helper\Logger $logger
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \CustomConcepts\Estimations\Api\EstimationDatesRepositoryInterface $estimationDatesRepository
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory,
        \Magento\Sales\Model\OrderFactory $orderModelFactory,
        \Magento\Sales\Model\ResourceModel\OrderFactory $orderResourceModelFactory,
        \CustomConcepts\Estimations\Helper\DeliveryDate $deliveryDateHelper,
        \Magento\Store\Api\StoreRepositoryInterface $storeRepository,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \CustomConcepts\Base\Helper\Logger $logger,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \CustomConcepts\Estimations\Api\EstimationDatesRepositoryInterface $estimationDatesRepository
    ) {
        $this->jsonResultFactory = $jsonResultFactory;
        $this->orderModelFactory = $orderModelFactory;
        $this->orderResourceModelFactory = $orderResourceModelFactory;
        $this->deliveryDateHelper = $deliveryDateHelper;
        $this->storeRepository = $storeRepository;
        $this->logger = $logger;
        $this->scopeConfig = $scopeConfig;
        $this->transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->estimationDatesRepository = $estimationDatesRepository;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        $this->logger->setLogFile('fulfillmentdashboard.log');
        $result = $this->jsonResultFactory->create();
        
        $data = $this->getRequest()->getParams();

        if($data == FALSE){
            return $result->setData('empty data');
        }

        $this->logger->info('Request from FD for changing ESD and informing customers:', is_array($data) ? $data : []);

        $authorise_key = md5($data['time'].':'.'SecretKeyForDashboard');
        if($authorise_key != $data['key']){
            $this->logger->info('Data does not match for auth; '.print_r($data,true).' our expected key:'.$authorise_key);
            die('Unauthorised to sent request.');
        }

        unset($data['key']);
        unset($data['time']);
        unset($data['is_oos']); //causes Can't find this order in magento. error in the foreach

        $response = [];
        try {
            foreach ($data as $orderNumber => $esd)
            {
                $order = $this->orderModelFactory->create()->loadByIncrementId((string)$orderNumber);
                if(!($order && $order->getEntityId())){
                    $response['failed'][$orderNumber] = 'Can\'t find this order in magento.';
                    continue;
                }

                $edd_orig =  $this->estimationDatesRepository->getLatest($order->getEntityId());
                $edd = $this->deliveryDateHelper->calculateEDD($esd, $order, false, false);

                if(strtotime($esd) <= strtotime($edd_orig->getData('shipping_date'))){
                    $response['failed'][$order->getIncrementId()] = 'Customer already informed about ESD further away or same day. '.$edd_orig->getData('shipping_date');
                    continue;
                }

                /** declare email template variables */
                $storeId = $order->getStoreId();
                $store = $this->storeRepository->getById($storeId);

                $emailTemplateVariables = [];
                $emailTemplateVariables['customer_name'] = $order->getShippingAddress()->getFirstname();
                $emailTemplateVariables['order_number'] = $order->getIncrementId();
                $emailTemplateVariables['StoreGroupName'] = $order->getStoreGroupName();

                //Get locale from ordered store. And set formatting date to this.
                $locale = $this->scopeConfig->getValue('general/locale/code', ScopeInterface::SCOPE_STORE, $storeId);
                if($edd) {
                    //esd in locale from order
                    $emailTemplateVariables['ESD'] = $this->formatDate($edd->getData('shipping_date'), $locale, $store->getCode());
                    //edd_min in store locale
                    $emailTemplateVariables['EDD_MIN'] = $this->formatDate($edd->getData('min_delivery_date'), $locale, $store->getCode());

                    if ($edd->getData('min_delivery_date') != $edd->getData('max_delivery_date')) {
                        //Edd max in locale only if max is different then min.
                        $emailTemplateVariables['EDD_MAX'] = $this->formatDate($edd->getData('max_delivery_date'), $locale, $store->getCode());
                    }
                }else{
                    //esd in locale from order
                    $emailTemplateVariables['ESD'] = $this->formatDate($esd, $locale, $store->getCode());
                }

                /** set sender */
                $marchentNotificationMailId  = $this->scopeConfig->getValue('reorderdiscountcode/qasim_email/qasim_sender_email_identity', ScopeInterface::SCOPE_STORE, $storeId);
                $sender = [
                    'name' => $this->scopeConfig->getValue("trans_email/ident_$marchentNotificationMailId/name", ScopeInterface::SCOPE_STORE, $storeId),
                    'email' => $this->scopeConfig->getValue("trans_email/ident_$marchentNotificationMailId/email", ScopeInterface::SCOPE_STORE, $storeId),
                ];

                /** get email template */
                $mailTemplateId = $this->scopeConfig->getValue('email/messages/template', ScopeInterface::SCOPE_STORE, $storeId);
                if(!$mailTemplateId){
                    $response['failed'][$orderNumber] = 'The mail template is not set up correctly for store: '.$store->getCode();
                    continue;
                }

                /** send email */
                $sent = $this->sendEmail($sender, $order->getCustomerEmail(), $mailTemplateId, $emailTemplateVariables, $storeId);

                if($sent){
                    $message = "Production delayed email sent, new ESD is " . $esd;
                    $order->addStatusToHistory($order->getStatus(), $message, true);

                    $orderResourceModel = $this->orderResourceModelFactory->create();
                    $orderResourceModel->save($order);

                    $response['success'][] = $orderNumber;
                } else {
                    $response['fail'][] = $orderNumber;
                }
            }

        } catch (Exeption $e){
            $response['failed']['1234567'] = 'Something went wrong:'.$e->getMessage();
            $this->logger->error($e->getMessage());
        }

        return $result->setData($response);
    }

    /**
     * @param $date date string
     * @param null $locale local value
     * @return false|string
     * @throws \Exception
     */
    protected function formatDate($date, $locale = null, $storeCode = null){
        $is_us = ['us_es', 'us_en', 'hm_us'];

        $date = new \DateTime($date);
        $formatter = new \IntlDateFormatter($locale, \IntlDateFormatter::LONG, \IntlDateFormatter::LONG);
        if (in_array($storeCode, $is_us) ) {
            $formatter->setPattern('MM-dd-Y');
        } else {
            $formatter->setPattern('dd-MM-Y');
        }

        return $formatter->format($date);
    }

    /**
     * @param $sender array value of sender
     * @param $to email to send
     * @param $templateId int|string of template id
     * @param $templateVars array of template variables
     * @param $storeId int id
     * @return bool
     */
    protected function sendEmail($sender, $to, $templateId, $templateVars, $storeId){
        $this->inlineTranslation->suspend();
        try {
            $transport = $this->transportBuilder
                ->setTemplateIdentifier($templateId)
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => $storeId,
                    ]
                )
                ->setTemplateVars($templateVars)
                ->setFrom($sender)
                ->addTo($to)
                ->getTransport();

            $transport->sendMessage();
            $this->inlineTranslation->resume();
            return true;
        } catch (\Exception $e) {
            echo $e->getMessage();
            $this->inlineTranslation->resume();
            return false;
        }
    }
}
