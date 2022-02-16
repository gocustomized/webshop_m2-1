<?php
namespace CustomConcepts\HelpCenter\Block;

use Magento\Framework\View\Element\Template;

class ContactForm extends \Magento\Framework\View\Element\Template {

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;
    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;
    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;
    /**
     * @var \Magento\Framework\Api\SortOrderBuilder
     */
    protected $sortOrderBuilder;

    /**
     * ContactForm constructor.
     * @param Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Framework\Api\SortOrderBuilder $sortOrderBuilder
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Api\SortOrderBuilder $sortOrderBuilder,
        array $data = [])
    {
        parent::__construct($context, $data);
        $this->customerSession = $customerSession;
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
    }

    /**
     * @return bool
     */
    public function isLoggedIn(){
        return $this->customerSession->isLoggedIn();
    }

    /**
     * @return \Magento\Customer\Model\Customer
     */
    public function getCustomer(){
        return $this->customerSession->getCustomer();
    }

    /**
     * @return string
     */
    public function getSendIssueFormAction(){
        return $this->getUrl('helpcenter/contactForm/sendIssueForm');
    }

    /**
     * @return \Magento\Framework\Data\Collection\AbstractDb|\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection|null
     */
    public function getOrders($filters = []){
        if($this->isLoggedIn()){
            foreach ($filters as $key => $value){
                $this->searchCriteriaBuilder->addFilter($key, $value);
            }

            $sortOrder = $this->sortOrderBuilder->setField('created_at')->setDirection('DESC')->create();
            $this->searchCriteriaBuilder->setSortOrders([$sortOrder]);

            $orderList = $this->orderRepository->getList($this->searchCriteriaBuilder->create());

            return $orderList->getItems();
        } else {
            return [];
        }
    }

    /**
     * @return false|string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getConfigs()
    {
        $configs = [
            [
                //My product(s)
                [
                    'label' => __('Name'),
                    'type' => 'text',
                    'placeholder' => __('Name'),
                    'required' => 'required',
                    'value' => $this->getCustomer()->getName(),
                    'name' => "name",
                    'readonly' => $this->isLoggedIn()
                ],
                [
                    'label' => __("Email address"),
                    'type' => 'e-mail',
                    'required' => 'required',
                    'value' => $this->isLoggedIn() ? $this->getCustomer()->getEmail() : '',
                    'name' => "e-mail",
                    'readonly' => $this->isLoggedIn()
                ],
//                [
//                    'label' => __("Order number"),
//                    'type' => 'number',
//                    'placeholder' => __('for example : 34769238'),
//                    'required' => 'required',
//                    'name' => "order_id"
//                ],
                [
                    'label' => __("Orders"),
                    'type' => 'product',
                    'required' => 'required',
                    'name' => "order_id"
                ],
                //Subcategory
                [
                    'label' => __("Question related to"),
                    'type' => 'issue',
                    'name' => "issue",
                    'required' => 'required'
                ],
                //Prefered solution
                [
                    'label' => __("My preferred solution"),
                    'type' => 'dropdown',
                    'name' => "solution",
                    'required' => 'required',
                    'values' => $this->getSolutions()
                ],
                [
                    'label' => __("Upload an image"),
                    'type' => 'file',
                    'name' => "image",
                    'required' => 'required'
                ],
                [
                    'label' => __("Description of the issue"),
                    'type' => 'textarea',
                    'placeholder' => __("Type your comment in 200 characters here..."),
                    'name' => "description"
                ]
            ],
            //My shipment
            [
                //Add sub-category
                [
                    'label' => __("Question related to"),
                    'type' => 'shipmentissues',
                    'name' => "shipmentissues",
                    'required' => 'required'
                ],
                [
                    'label' => __("Name"),
                    'type' => 'text',
                    'placeholder' => __("Name"),
                    'required' => 'required',
                    'value' => $this->isLoggedIn() ? $this->getCustomer()->getName() : '',
                    'name' => "name",
                    'readonly' => $this->isLoggedIn()
                ],
                [
                    'label' => __("Email address"),
                    'type' => 'e-mail',
                    'required' => 'required',
                    'value' => $this->isLoggedIn() ? $this->getCustomer()->getEmail() : '',
                    'name' => "e-mail",
                    'readonly' => $this->isLoggedIn()
                ],
//                [
//                    'label' => __('Order number?'),
//                    'type' => 'number',
//                    'placeholder' => __("for example : 34769238"),
//                    'required' => 'required',
//                    'name' => "order_id"
//                ],
                [
                    'label' => __("Orders"),
                    'type' => 'product',
                    'required' => 'required',
                    'name' => "order_id"
                ]
            ],
            //Cancel order
            [
                [
                    'label' => __("Name"),
                    'type' => 'text',
                    'placeholder' => __("Name"),
                    'required' => 'required',
                    'value' => $this->isLoggedIn() ? $this->getCustomer()->getName() : '',
                    'name' => "name",
                    'readonly' => $this->isLoggedIn()
                ],
                [
                    'label' => __("Email address"),
                    'type' => 'e-mail',
                    'required' => 'required',
                    'value' => $this->isLoggedIn() ? $this->getCustomer()->getEmail() : '',
                    'name' => "e-mail",
                    'readonly' => $this->isLoggedIn()
                ],
//                [
//                    'label' => __("Order number"),
//                    'type' => 'number',
//                    'placeholder' => __("for example : 34769238"),
//                    'required' => 'required',
//                    'name' => "order_id"
//                ],
                [
                    'label' => __("Orders"),
                    'type' => 'product',
                    'required' => 'required',
                    'name' => "order_id"
                ],
                [
                    'label' => __("Reason for cancelation"),
                    'type' => 'cancel',
                    'name' => "cancel",
                    'required' => 'required'
                ],
                [
                    'label' => __("Description of the issue"),
                    'type' => 'textarea',
                    'placeholder' => __("Type your comment in 200 characters here..."),
                    'name' => "description"
                ]
            ],
            //Other
            [
                [
                    'label' => __('Name'),
                    'type' => 'text',
                    'placeholder' => __('Name'),
                    'required' => 'required',
                    'value' => $this->isLoggedIn() ? $this->getCustomer()->getName() : '',
                    'name' => "name",
                    'readonly' => $this->isLoggedIn()
                ],
                [
                    'label' => __("Email address"),
                    'type' => 'e-mail',
                    'required' => 'required',
                    'value' => $this->isLoggedIn() ? $this->getCustomer()->getEmail() : '',
                    'name' => "e-mail",
                    'readonly' => $this->isLoggedIn()
                ],
                [
                    'label' => __("Description of the issue"),
                    'type' => 'textarea',
                    'placeholder' => __("Type your comment in 200 characters here..."),
                    'name' => "description"
                ]
            ]
        ];

        return json_encode($configs);
    }

    /**
     * @return array[]
     */
    public function getSolutions(){
        $solutions = [
            ["", __("Choose your solution")],
            ["reorder", __("I would like to receive a new one")],
            ["refund", __("I would like to get a refund")]
        ];
        return $solutions;
    }

    /**
     * @return false|string
     */
    public function getCancelationReasons(){
        $cancelationReasons = [
            ["", ""],
            ["product_wrong", __("Wrong product")],
            ["changed_mind", __("Changed mind")]
        ];
        return json_encode($cancelationReasons);
    }

    /**
     * @return false|string
     */
    public function getShipmentIssues(){
        $shipmentissues = [
            ["", __("Select subcategory")],
            ['package_not_yet_arrived', __("Package not yet arrived")],
            ['request_to_change_delivery_address', __("Request to change delivery address")],
            ['request_for_cancellation', __("Request for cancellation")]
        ];
        return json_encode($shipmentissues);
    }

    /**
     * @return false|string
     */
    public function getIssues(){
        $issues = [
            ["", __("Select subcategory")],
            ['quality_print_image', __("Print quality")],
            ['quality_item_case', __("Product quality (item)")],
            ['wrong_product', __("Wrong product (does not fit)")],
            ['not_expected', __("It is not what I expected")],
            ['package_not_yet_arrived', __("Package not yet arrived")]
        ];
        return json_encode($issues);
    }

    /**
     * returns custoemr forgot password url
     */
    public function getForgotPasswordUrl()
    {
        return $this->_urlBuilder->getUrl('customer/account/forgotpassword');
    }
}
