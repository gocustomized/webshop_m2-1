<?php
namespace CustomConcepts\HelpCenter\Controller\ContactForm;

use Magento\Framework\App\Action\Context;
use \Magento\Store\Model\ScopeInterface;

class SendIssueForm extends \Magento\Framework\App\Action\Action {
    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $mediaDirectory;
    /**
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    protected $fileUploaderFactory;
    /**
     * @var \Magento\Customer\Model\Customer
     */
    protected $customerModel;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @var \Magento\Sales\Model\Order
     */
    protected $salesOrder;
    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;
    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;
    /**
     * @var \CustomConcepts\Estimations\Api\EstimationDatesRepositoryInterface
     */
    protected $estimationDatesRepository;
    /**
     * @var \CustomConcepts\Estimations\Model\DeliveryRate
     */
    protected $deliveryRate;
    /**
     * @var \CustomConcepts\Prodfaqs\Model\ZendeskRepository
     */
    protected $zendeskRepository;
    /**
     * @var \Bluebirdday\TranssmartSmartConnect\Model\Shipping\Method\Parser
     */
    protected $shippingMethodParser;
    /**
     * @var \Bluebirdday\TranssmartSmartConnect\Model\CarrierFactory
     */
    protected $carrierFactory;
    /**
     * @var \Bluebirdday\TranssmartSmartConnect\Model\ResourceModel\Carrier
     */
    protected $carrierResourceModel;

    protected $file;

    protected $date;


    /**
     * SendIssueForm constructor.
     * @param Context $context
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory
     * @param \Magento\Customer\Model\Customer $customerModel
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Sales\Model\Order $salesOrder
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \CustomConcepts\Estimations\Api\EstimationDatesRepositoryInterface $estimationDatesRepository
     * @param \CustomConcepts\Estimations\Model\DeliveryRate $deliveryRate
     * @param \CustomConcepts\HelpCenter\Model\ZendeskRepository $zendeskRepository
     * @param \Bluebirdday\TranssmartSmartConnect\Model\Shipping\Method\Parser $shippingMethodParser
     * @param \Bluebirdday\TranssmartSmartConnect\Model\CarrierFactory $carrierFactory
     * @param \Bluebirdday\TranssmartSmartConnect\Model\ResourceModel\Carrier $carrierResourceModel
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Magento\Customer\Model\Customer $customerModel,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Sales\Model\Order $salesOrder,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \CustomConcepts\Estimations\Api\EstimationDatesRepositoryInterface $estimationDatesRepository,
        \CustomConcepts\Estimations\Model\DeliveryRate $deliveryRate,
        \CustomConcepts\HelpCenter\Model\ZendeskRepository $zendeskRepository,
        \Bluebirdday\TranssmartSmartConnect\Model\Shipping\Method\Parser $shippingMethodParser,
        \Bluebirdday\TranssmartSmartConnect\Model\CarrierFactory $carrierFactory,
        \Bluebirdday\TranssmartSmartConnect\Model\ResourceModel\Carrier $carrierResourceModel,
        \Magento\Framework\Filesystem\Io\File $file,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date
    )
    {
        parent::__construct($context);

        $this->mediaDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $this->fileUploaderFactory = $fileUploaderFactory;
        $this->customerModel = $customerModel;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->salesOrder = $salesOrder;
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->estimationDatesRepository = $estimationDatesRepository;
        $this->deliveryRate = $deliveryRate;
        $this->zendeskRepository = $zendeskRepository;
        $this->shippingMethodParser = $shippingMethodParser;
        $this->carrierFactory = $carrierFactory;
        $this->carrierResourceModel = $carrierResourceModel;
        $this->file = $file;
        $this->date = $date;
    }

    /**
     * @return bool|string
     */
    protected function uploadFile()
    {
        try{
            $zendesk_dir = "upload_images/zendesk/";
            $target = $this->mediaDirectory->getAbsolutePath($zendesk_dir);

            $this->file->checkAndCreateFolder($target);

            $uploader = $this->fileUploaderFactory->create(['fileId' => 'image']); //input name
            $uploader->setAllowedExtensions(['jpg','png','jpeg']);
            $uploader->setAllowCreateFolders(true);
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(false);
            $result = $uploader->save($target);

            $mediaUrl = $this->storeManager-> getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA );

            return $mediaUrl . $zendesk_dir . $result['file'];
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Layout
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        $data = $this->getRequest()->getPost();
        $referrerRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
        $referrerRedirect->setUrl($this->_redirect->getRefererUrl());
        $contactFormBlock = $this->_view->getLayout()->createBlock('CustomConcepts\HelpCenter\Block\ContactForm');

        if(empty($data['subject'])){
            $data['subject'] = 'Process form request no subject.';
        }
        if (isset($_FILES['image']) && $_FILES['image'] != '') {
            $data['image'] = $this->uploadFile();
            if(!$data['image']){
                $this->messageManager->addErrorMessage(__('Error uploading the file'));

                return $referrerRedirect;
            }
        }

        if($data){
            $requesterId = null;

            if($contactFormBlock->isLoggedIn()){
                $requesterEmail = $contactFormBlock->getCustomer()->getEmail();
                $requesterName = $contactFormBlock->getCustomer()->getName();
            } else {
                $requesterEmail = trim($data['e-mail']);
                $requesterName = trim($data['name']);
            }

            $customer = null;
            if($this->customerModel->getSharingConfig()->isWebsiteScope()) {
                // Customer email address can be used in multiple websites so we need to
                // explicitly scope it
                $customer = $this->customerModel->setWebsiteId($this->storeManager->getWebsite()->getId())
                            ->loadByEmail($requesterEmail);
            } else {
                // Customer email is global, so no scoping issues
                $customer = $this->customerModel->loadByEmail($requesterEmail);
            }

            if($customer->getId()) {
                // Provided for future expansion, where we might want to store the customer's requester ID for
                // convenience; for now it simply returns null
                $requesterId = $customer->getZendeskRequesterId();

                // If the requester name hasn't already been set, then set it to the customer name
                if(strlen($requesterName) == 0) {
                    $requesterName = $customer->getName();
                }
            }

            if($requesterId == null) {
                $userData = [
                    'user' => [
                        'name' => $requesterName,
                        'email' => $requesterEmail
                    ]
                ];

                $response = $this->zendeskRepository->createOrUpdateUser($userData);
                $requesterId = $response->user->id;
            }

            $response = '';
            try {
                $orderList = $contactFormBlock->getOrders(['customer_email' => $requesterEmail,
                                                            'increment_id' => $data['order_id']]);
                $order = current($orderList);

                $storeId = $order ? $order->getStore()->getId() : $this->storeManager->getStore()->getId();

                $ticket = array(
                    'ticket' => array(
                        'requester_id' => $requesterId,
                        'subject' => $data['subject'],
                        'recipient' => $this->scopeConfig->getValue('trans_email/ident_support/email', ScopeInterface::SCOPE_STORE, $storeId),
                        'description' => $data['description']?:'Request from contactform.',
                        'status' => 'new',
                        'priority' => 'normal',
//                        'type'     => 'ticket',
                        'type'     => 'task',
                        'comment' => array(
                            'value' => $data['description']?:'Request from contactform.'
                        )
                    )
                );

                if( ($fieldId = $this->scopeConfig->getValue('zendesk/features/order_field_id')) && isset($data['order_id']) && strlen(trim($data['order_id'])) > 0)
                {
                    if($order && $order->getId()) {
                        $ticket['ticket']['custom_fields'][] = array(
                            'id' => $fieldId,
                            'value' => $data['order_id']
                        );

                        if(($statusId = $this->scopeConfig->getValue('zendesk/features/status_field_id'))){
                            $ticket['ticket']['custom_fields'][] = array(
                                'id' => $statusId,
                                'value' => $order->getStatus()
                            );
                        }
                    }else{
                        $this->messageManager->addErrorMessage('Could not find matching order with this email-address.');
                        return $referrerRedirect;
                    }

                }

                $this->putShipmentInfo($data, $ticket, $order);

                if( ($fieldId = $this->scopeConfig->getValue('zendesk/features/image_field_id')) && isset($data['image']) && strlen(trim($data['image'])) > 0) {
                    $ticket['ticket']['custom_fields'][] = array(
                        'id' => $fieldId,
                        'value' => $data['image']
                    );
                }
                // Subcategory field product related (one dropdown zendesk)
                if( ($fieldId = $this->scopeConfig->getValue('zendesk/features/type_issue_field_id')) && isset($data['issue']) && strlen(trim($data['issue'])) > 0) {
                    $ticket['ticket']['custom_fields'][] = array(
                        'id' => $fieldId,
                        'value' => $data['issue']
                    );
                }
                // Subcategory field shipment related (one dropdown zendesk)
                if( ($fieldId = $this->scopeConfig->getValue('zendesk/features/type_issue_field_id')) && isset($data['shipmentissues']) && strlen(trim($data['shipmentissues'])) > 0) {
                    $ticket['ticket']['custom_fields'][] = array(
                        'id' => $fieldId,
                        'value' => $data['shipmentissues']
                    );
                }
                //Prefered solution field
                if( isset($data['solution']) && strlen(trim($data['solution'])) > 0 ) {
                    if(($fieldId = $this->scopeConfig->getValue('zendesk/features/solution_field_id'))){
                        $ticket['ticket']['custom_fields'][] = array(
                            'id' => $fieldId,
                            'value' => $data['solution']
                        );
                    }
                }

                //Reason for Cancellation
                if( isset($data['reason_cancel']) && strlen(trim($data['reason_cancel'])) > 0 ) {
                    if(($fieldId = $this->scopeConfig->getValue('zendesk/features/reason_cancel'))){
                        $ticket['ticket']['custom_fields'][] = array(
                            'id' => $fieldId,
                            'value' => $data['reason_cancel']
                        );
                    }
                }

                if( ($fieldId = $this->scopeConfig->getValue('zendesk/features/support_category_field_id')) && isset($data['subject']) && strlen(trim($data['subject'])) > 0) {
                    $keyszendesk = [
                        'My product(s)' => 'product',
                        'My shipment'   => 'shipment',
                        'Other'         => 'other',
                        'Cancel order'  => 'cancel_order'
                    ];
                    $ticket['ticket']['custom_fields'][] = array(
                        'id' => $fieldId,
                        'value' => $keyszendesk[$data['subject']]
                    );
                }

                if(!$this->scopeConfig->getValue('envconfig_options/general/env_field_api')){
                    $ticket['ticket']['tags'] = array('staging');
                }
                $ticket['ticket']['tags'][] = $this->storeManager->getStore()->getCode();

                $response = $this->zendeskRepository->createTicket($ticket);
                $this->log('Zendesk ticket Request: '.print_r($ticket,true));
                $this->messageManager->addSuccessMessage(__("Thanks for reaching out to us we'll soon be in contact."));
            } catch(Exception $e) {
                $this->log('Zendesk error: '.$e->getMessage() . 'Request: '.print_r($ticket,true)."\n".'Response:'.print_r($response,true));
                $this->messageManager->addErrorMessage($e->getMessage());
                return $referrerRedirect;
            }

        }

        return $referrerRedirect;
    }

    /**
     * @param $request
     * @param $ticket
     * @param null $order
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function putShipmentInfo($request, &$ticket, $order = null)
    {
        if( isset($request['order_id']) && strlen(trim($request['order_id'])) > 0) {
            if (!empty($order) && $order->getId()) {
                $edd = $this->estimationDatesRepository->getLatest($order->getEntityId());

                $shipments = $order->getShipmentsCollection();

                foreach ($shipments as $shipment) {
                    $tracking_url = $shipment->getData('transsmart_tracking_url');
                    if ($tracking_url && ($ttfieldId = $this->scopeConfig->getValue('zendesk/features/track_and_trace_field_id'))) {
                        $ticket['ticket']['custom_fields'][] = array(
                            'id' => $ttfieldId,
                            'value' => $tracking_url
                        );
                    }

                    if ($shipfieldId = $this->scopeConfig->getValue('zendesk/features/shipmentdate_field_id')) {

                        //Sent as date field format: date ("Y-m-d")
                        if (!empty($shipment->getCreatedAt()) && !$shipment->getGhostShipment()) {
                            $shipment_date = date("Y-m-d", strtotime($shipment->getCreatedAt()));
                            //Sent as date field format: date ("Y-m-d")
                            $ticket['ticket']['custom_fields'][] = array(
                                'id' => $shipfieldId,
                                'value' => $shipment_date
                            );
                        }
                    }
                }

                if ($carrierfieldId = $this->scopeConfig->getValue('zendesk/features/carrier_field_id')) {
                    $shipping_method_info = $this->getShippingMethod($order);
                    $ticket['ticket']['custom_fields'][] = array(
                        'id' => $carrierfieldId,
                        'value' => $shipping_method_info['name']
                    );

                    if(($carrierCodeFieldId = $this->scopeConfig->getValue('zendesk/features/carrier_code_field_id'))){
                        //To only sent carrier code for automations
                        $ticket['ticket']['custom_fields'][] = array(
                            'id' => $carrierCodeFieldId,
                            'value' => $shipping_method_info['code']
                        );
                    }
                }


                if (($esd = $edd->getData('shipping_date')) && ($esdfieldId = $this->scopeConfig->getValue('zendesk/features/esd_field_id'))) {
                    //Sent as date field format: date ("Y-m-d")
                    $ticket['ticket']['custom_fields'][] = array(
                        'id' => $esdfieldId,
                        'value' => date("Y-m-d", strtotime($esd))
                    );
                }

                if ($brandfieldId = $this->scopeConfig->getValue('zendesk/features/brand_field_id')) {
                    $ticket['ticket']['custom_fields'][] = array(
                        'id' => $brandfieldId,
                        'value' => $order->getStore()->getWebsite()->getCode()
                    );
                }
                if ($countryfieldId = $this->scopeConfig->getValue('zendesk/features/country_field_id')) {
                    $ticket['ticket']['custom_fields'][] = array(
                        'id' => $countryfieldId,
                        'value' => $this->scopeConfig->getValue('general/locale/code', ScopeInterface::SCOPE_STORE, $order->getStore()->getId())
                    );
                }


                if (($edd_min = $edd->getData('min_delivery_date')) && ($eddMinfieldId = $this->scopeConfig->getValue('zendesk/features/edd_min_field_id'))) {
                    //Sent as date field format: date ("Y-m-d")
                    $ticket['ticket']['custom_fields'][] = array(
                        'id' => $eddMinfieldId,
                        'value' => date("Y-m-d", strtotime($edd_min))
                    );
                }
                if (($edd_max = $edd->getData('max_delivery_date')) && ($eddMaxfieldId = $this->scopeConfig->getValue('zendesk/features/edd_max_field_id'))) {
                    //Sent as date field format: date ("Y-m-d")
                    $ticket['ticket']['custom_fields'][] = array(
                        'id' => $eddMaxfieldId,
                        'value' => date("Y-m-d", strtotime($edd_max))
                    );

                    //Sent as date field format: date ("Y-m-d")
                    $ticket['ticket']['due_at'] = date("Y-m-d", strtotime($edd_max));

                    $working_days_between_edd_and_now = $this->getNumberOfWorkingDays($edd);
                    if ($working_days_between_edd_and_now >= 0) {
                        if(($workingDaysFieldId = $this->scopeConfig->getValue('zendesk/features/delay_in_working_days_field_id'))){
                            $ticket['ticket']['custom_fields'][] = array(
                                'id' => $workingDaysFieldId,
                                'value' => $working_days_between_edd_and_now
                            );
                        }
                    }
                }

                if(($createdAtFieldId = $this->scopeConfig->getValue('zendesk/features/created_at_field_id'))){
                    $ticket['ticket']['custom_fields'][] = array(
                        'id' => $createdAtFieldId,
                        'value' => date("Y-m-d", strtotime($order->getCreatedAt()))
                    );
                }

                //Send single or multiple item order
                if(($fieldId = $this->scopeConfig->getValue('zendesk/features/single_or_multiple_field_id'))){
                    $single_or_multiple = $this->checkSingleOrMultipleItemOrder($order);
                    $ticket['ticket']['custom_fields'][] = array(
                        'id' => $fieldId,
                        'value' => $single_or_multiple
                    );
                }

                return;
            }
        }
        if($brandfieldId = $this->scopeConfig->getValue('zendesk/features/brand_field_id')){
            $ticket['ticket']['custom_fields'][] = array(
                'id' => $brandfieldId,
                'value' => $this->storeManager->getWebsite()->getCode()
            );
        }
        if($countryfieldId = $this->scopeConfig->getValue('zendesk/features/country_field_id')){
            $ticket['ticket']['custom_fields'][] = array(
                'id' => $countryfieldId,
                'value' => $this->scopeConfig->getValue('general/locale/code', ScopeInterface::SCOPE_STORE, $this->storeManager->getStore()->getId())
            );
        }
    }

    /**
     * @param $edd
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    function getNumberOfWorkingDays($edd) {
        $from        = $edd->getData('max_delivery_date');
        $to          = date('Y-m-d');

        //Get current date to calculate offset of CEST and GMT.
        $locale = $this->scopeConfig->getValue('general/locale/code', ScopeInterface::SCOPE_STORE, $this->storeManager->getStore()->getId());
        $offset = $this->getOffset($locale);
        $gmt_hour_now      = date('G') + $offset;

        if($gmt_hour_now < 20){ // GMT for 22.00 CEST
            $to .= ' 00:00:00';
        }else{
            $to .= ' 01:00:00';
        }
        $this->log('WD from:' . $from . ', to:' . $to);

        $rate = $this->deliveryRate->load($edd->getData('delivery_rate_id'));
        if($rate && $rate->getId() && $rate->getWeekend()){
            $weekendDays = str_split($rate->getWeekend());
            $this->log('Rate used:'.$rate->getId(), 'edd.log');
        }else{
            $weekendDays = [0, 0, 0, 0, 0, 1, 1];
        }
        $this->log('Weekend days used:'.print_r($weekendDays,true), 'edd.log');
        $holidayDays = [];//['*-12-25', '*-01-01', '2013-12-23']; # variable and fixed holidays

        $str_from = strtotime($from);
        $str_to = strtotime($to);

        $from = new \DateTime($from);
        $to = new \DateTime($to);
        $interval = new \DateInterval('P1D');

        //Exclude edd_max if is same day before 22.00 CEST
        if($str_from == $str_to && $gmt_hour_now < 20){
            $periods = new \DatePeriod($from, $interval, $to, \DatePeriod::EXCLUDE_START_DATE);
        }else{
            $periods = new \DatePeriod($from, $interval, $to);
        }

        $days = 0;
        foreach ($periods as $period) {
            $this->log('Processing:'. $period->format('Y-m-d').' day:'.($period->format('N')-1).'isweekend?'.print_r($weekendDays[$period->format('N')-1], true), 'edd.log');

            if ($weekendDays[$period->format('N')-1] == 1){
                $this->log('Skipping cause its weekend', 'edd.log');
                $this->log('Skipping'.print_r($period->format('N')-1,true).' cause its falling in weekend', 'edd.log');
                continue;
            }
            $this->log('Counting'.print_r($period->format('N')-1,true).' as working day', 'edd.log');
            $days++;
        }

        $this->log('Returning wd'.$days, 'edd.log');
        return $days;
    }

    /**
     * @param $order
     * @return string
     */
    private function checkSingleOrMultipleItemOrder($order)
    {
        $totalQty       = $order->getTotalQtyOrdered();
        $i              = 0;
        $items          = $order->getAllItems();
        $giftwrappings  = ['GIFTWRAP-2' => 91313100406, 'GIFTWRAP-1' => 91312100406, 'DELUXE-1' => 91314100406, 'DELUXE-2' => 91326100406, 'DELUXE-3' => 91323100406, 'SPEAKER GIFT' => 91311100401, 'MUG GIFT' => 91320100402];

        foreach ($items as $item) {
            if (in_array($item->getSku(), $giftwrappings)) {
                continue;
            }
            $i += $item->getQtyOrdered();
        }
        $this->log('Order '.$order->getId().' has '.$i . ' items.');
        return ($i == 1 ? 'single' : 'multi' );
    }

    /**
     * @param $locale
     * @return float|int
     */
    protected function getOffset($locale){
        $origLocale = locale_get_default();
        setlocale(LC_TIME, $locale);

        $date = new \DateTime();
        $offset = $date->getOffset()/3600;

        setlocale(LC_TIME, $origLocale);

        return $offset;
    }

    /**
     * @param $order
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getShippingMethod($order){
        $bookingProfile = $this->shippingMethodParser->parseOrder($order);
        $orderShippingMethod = $order->getShippingMethod();

        $shippingMethod = [
            'name' => $orderShippingMethod,
            'code' => trim(substr($orderShippingMethod, 0, strpos($orderShippingMethod, '_')))
        ];

        if($bookingProfile){
            $carrierId = $bookingProfile->getCarrierId();
            $c_code = $bookingProfile->getCode();
            $carrier = $this->carrierFactory->create();
            $this->carrierResourceModel->load($carrier, $carrierId);

            if($carrier){
                $shippingMethod['name'] = $carrier->getName();
                $shippingMethod['code'] = $c_code;
            }
        }

        return $shippingMethod;
    }

    public function log($message, $file = 'zendesk.log'){
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/' . $file);
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info($message);
    }

}
