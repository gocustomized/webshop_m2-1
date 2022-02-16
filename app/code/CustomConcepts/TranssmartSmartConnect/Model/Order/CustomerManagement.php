<?php
namespace CustomConcepts\TranssmartSmartConnect\Model\Order;


use Magento\Customer\Api\Data\AddressInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Quote\Model\Quote\Address as QuoteAddress;
use Magento\Quote\Model\Quote\AddressFactory as QuoteAddressFactory;
use Magento\Sales\Api\Data\OrderAddressInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order\OrderCustomerExtractor;

class CustomerManagement extends \Magento\Sales\Model\Order\CustomerManagement
{
    protected $carrierRepository;
    protected $profileRepository;
    protected $estimationsHelper;
    /**
     * @var QuoteAddressFactory
     */
    private $quoteAddressFactory;

    /**
     * @var OrderCustomerExtractor
     */
    private $customerExtractor;

    public function __construct(
        \Magento\Framework\DataObject\Copy $objectCopyService,
        \Magento\Customer\Api\AccountManagementInterface $accountManagement,
        \Magento\Customer\Api\Data\CustomerInterfaceFactory $customerFactory,
        \Magento\Customer\Api\Data\AddressInterfaceFactory $addressFactory,
        \Magento\Customer\Api\Data\RegionInterfaceFactory $regionFactory,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Bluebirdday\TranssmartSmartConnect\Model\CarrierRepository $carrierRepository,
        \Bluebirdday\TranssmartSmartConnect\Model\Booking\ProfileRepository $profileRepository,
        \CustomConcepts\Estimations\Helper\Data $estimationsHelper,
        QuoteAddressFactory $quoteAddressFactory = null,
        OrderCustomerExtractor $orderCustomerExtractor = null
    ){
        $this->carrierRepository = $carrierRepository;
        $this->profileRepository = $profileRepository;
        $this->estimationsHelper = $estimationsHelper;
        $this->quoteAddressFactory = $quoteAddressFactory
            ?: ObjectManager::getInstance()->get(QuoteAddressFactory::class);
        $this->customerExtractor = $orderCustomerExtractor
            ?? ObjectManager::getInstance()->get(OrderCustomerExtractor::class);
        parent::__construct($objectCopyService, $accountManagement, $customerFactory, $addressFactory, $regionFactory, $orderRepository, $quoteAddressFactory, $orderCustomerExtractor);
    }

    /**
     * {@inheritdoc}
     */
    public function create($orderId)
    {
        $order = $this->orderRepository->get($orderId);
        if ($order->getCustomerId()) {
            throw new AlreadyExistsException(
                __('This order already has associated customer account')
            );
        }

        $customer = $this->customerExtractor->extract($orderId);
        /** @var AddressInterface[] $filteredAddresses */
        $filteredAddresses = [];
        foreach ($customer->getAddresses() as $address) {
            if ($this->needToSaveAddress($order, $address)) {
                $filteredAddresses[] = $address;
            }
        }
        $customer->setAddresses($filteredAddresses);

        $account = $this->accountManagement->createAccount($customer);
        $order = $this->orderRepository->get($orderId);
        $order->setCustomerId($account->getId());
        $order->setCustomerIsGuest(0);
        $this->orderRepository->save($order);

        return $account;
    }

    /**
     * @param OrderInterface $order
     * @param AddressInterface $address
     *
     * @return bool
     */
    private function needToSaveAddress(
        OrderInterface $order,
        AddressInterface $address
    ): bool {
        if($this->checkIfBillingIsFromTranssmart($order, $address)){
            return false;
        }

        /** @var OrderAddressInterface|null $orderAddress */
        $orderAddress = null;
        if ($address->isDefaultBilling()) {
            $orderAddress = $order->getBillingAddress();
        } elseif ($address->isDefaultShipping()) {
            $orderAddress = $order->getShippingAddress();
        }
        if ($orderAddress) {
            $quoteAddressId = $orderAddress->getData('quote_address_id');
            if ($quoteAddressId) {
                /** @var QuoteAddress $quote */
                $quote = $this->quoteAddressFactory->create()
                    ->load($quoteAddressId);
                if ($quote && $quote->getId()) {
                    return (bool)(int)$quote->getData('save_in_address_book');
                }
            }

            return true;
        }
        return false;
    }

    public function checkIfBillingIsFromTranssmart(
        OrderInterface $order,
        AddressInterface $address
    ){
        if($address->isDefaultShipping()){
            $shippingMethod = $order->getShippingMethod();
            $shippingMethodArr = explode('_', $shippingMethod);
            $profileCode = end($shippingMethodArr);

            $bookingProfile = $this->profileRepository->loadByCode($profileCode);
            $carrier = $this->carrierRepository->load($bookingProfile->getCarrierId());

            $shippingMethodConfig = $this->estimationsHelper->getShippingMethodConfig($profileCode);
            $homeDelivery = $this->estimationsHelper->getShippingInfo($shippingMethodConfig, 'home_delivery');

            if($carrier->getLocationSelect() && !$homeDelivery){
                return true;
            }
        }
        return false;
    }
}
