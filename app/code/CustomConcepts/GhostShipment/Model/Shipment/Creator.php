<?php

namespace CustomConcepts\GhostShipment\Model\Shipment;

use Bluebirdday\TranssmartSmartConnect\Model\Booking\ProfileRepository;
use Bluebirdday\TranssmartSmartConnect\Model\CarrierRepository;
use Bluebirdday\TranssmartSmartConnect\Model\CostcenterRepository;
use Bluebirdday\TranssmartSmartConnect\Model\Data\AdditionalReferencesFactory;
use Bluebirdday\TranssmartSmartConnect\Model\Data\MeasurementsFactory;
use Bluebirdday\TranssmartSmartConnect\Model\Data\PackageFactory;
use Bluebirdday\TranssmartSmartConnect\Model\IncotermRepository;
use Bluebirdday\TranssmartSmartConnect\Model\PackageRepository;
use Bluebirdday\TranssmartSmartConnect\Model\Product\Attribute\Mapper as AttributeMapper;
use Bluebirdday\TranssmartSmartConnect\Model\Servicelevel\OtherRepository as ServiceLevelOtherRepository;
use Bluebirdday\TranssmartSmartConnect\Model\Servicelevel\TimeRepository as ServiceLevelTimeRepository;
use Bluebirdday\TranssmartSmartConnect\Model\Street\Address\Mapper;
use Bluebirdday\TranssmartSmartConnect\Model\Street\Address\Parser;
use Bluebirdday\TranssmartSmartConnectExtension\Model\Shipment\Creator as TSCreator;
use CustomConcepts\TranssmartSmartConnect\Model\Data\Address as CCAddress;
use Magento\Customer\Model\Group as CustomerGroup;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Json\Helper\Data;
use Magento\Sales\Model\Order\Shipment;
use Magento\Store\Model\Information;
use Magento\Store\Model\ScopeInterface as ScopeInterfaceAlias;


/**
 * Overrides of Bluebirdday\TranssmartSmartConnectExtension\Model\Shipment\Creator:
 * 1. Added extra additional references for FD dashboard in initAdditionalReferences
 * 2. Added EORI number to SEND address
 */

class Creator extends TSCreator
{
    /**
     * @var CustomerGroup
     */
    protected $customerGroupCollection;
    /**
     * @var AdditionalReferencesFactory
     */
    protected $notPrivateAdditionalRefFactory;
    /**
     * @var ScopeConfigInterface
     */
    protected $notPrivateScopeConfig;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    const XML_EORI_PATH = 'general/store_information/eori_number';
    /**
     * @var Mapper
     */
    private $notPrivateStreetMapper;
    /**
     * @var Parser
     */
    private $notPrivateStreetParser;

    /**
     * Creator constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param Data $jsonHelper
     * @param Mapper $streetMapper
     * @param AttributeMapper $productAttributeMapper
     * @param Parser $streetParser
     * @param PackageRepository $packageRepository
     * @param PackageFactory $packageFactory
     * @param MeasurementsFactory $measurementsFactory
     * @param ProfileRepository $profileRepository
     * @param AdditionalReferencesFactory $additionalReferencesFactory
     * @param ProfileRepository $bookingProfileRepository
     * @param CarrierRepository $carrierRepository
     * @param ServiceLevelOtherRepository $serviceLevelOtherRepository
     * @param ServiceLevelTimeRepository $serviceLevelTimeRepository
     * @param IncotermRepository $incotermRepository
     * @param CostcenterRepository $costcenterRepository
     * @param CustomerGroup $customerGroupCollection
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Data $jsonHelper,
        Mapper $streetMapper,
        AttributeMapper $productAttributeMapper,
        Parser $streetParser,
        PackageRepository $packageRepository,
        PackageFactory $packageFactory,
        MeasurementsFactory $measurementsFactory,
        ProfileRepository $profileRepository,
        AdditionalReferencesFactory $additionalReferencesFactory,
        ProfileRepository $bookingProfileRepository,
        CarrierRepository $carrierRepository,
        ServiceLevelOtherRepository $serviceLevelOtherRepository,
        ServiceLevelTimeRepository $serviceLevelTimeRepository,
        IncotermRepository $incotermRepository,
        CostcenterRepository $costcenterRepository,
        //CUSTOM
        CustomerGroup $customerGroupCollection,
        \Psr\Log\LoggerInterface $logger)

    {
        parent::__construct(
            $scopeConfig,
            $jsonHelper,
            $streetMapper,
            $productAttributeMapper,
            $streetParser,
            $packageRepository,
            $packageFactory,
            $measurementsFactory,
            $profileRepository,
            $additionalReferencesFactory,
            $bookingProfileRepository,
            $carrierRepository,
            $serviceLevelOtherRepository,
            $serviceLevelTimeRepository,
            $incotermRepository,
            $costcenterRepository
        );
        $this->logger = $logger;
        $this->notPrivateScopeConfig = $scopeConfig;
        $this->customerGroupCollection = $customerGroupCollection;
        $this->notPrivateAdditionalRefFactory = $additionalReferencesFactory;
        $this->notPrivateStreetMapper = $streetMapper;
        $this->notPrivateStreetParser = $streetParser;
    }


    /**
     * Modify $shipmentData with 3 address types: sender, receiver, invoice address
     *
     * @param \Bluebirdday\TranssmartSmartConnect\Model\Data\Shipment $shipmentData
     * @param \Magento\Sales\Model\Order\Shipment $shipment
     */
    public function initAddresses(
        \Bluebirdday\TranssmartSmartConnect\Model\Data\Shipment $shipmentData,
        \Magento\Sales\Model\Order\Shipment $shipment
    ) {
        $addresses = [];

        $senderData = $this->getSenderData($shipment->getStore()->getId());
        $addresses[] = new CCAddress($senderData);

        $receiverAddress = $shipment->getShippingAddress();
        $invoiceAddress = $shipment->getBillingAddress();

        $receiverData = $this->getOrderAddressData(
            $receiverAddress,
            $shipment->getStore(),
            'RECV'
        );

        $invoiceData = $this->getOrderAddressData($invoiceAddress, $shipment->getStore(), 'INVC');

        $addresses[] = new CCAddress($receiverData);
        $addresses[] = new CCAddress($invoiceData);

        $shipmentData->setData('addresses', $addresses);
    }

    /**
     *
     * @param \Magento\Sales\Model\Order\Address $address
     * @param null $store
     * @param string|null $type
     * @return array
     */
    public function getOrderAddressData($address, $store = null, $type = null)
    {
        $streetFields = $this->notPrivateStreetMapper->getStreetFields($address, $store);

        $addressData = [
            'name' => $address->getCompany() ?: $address->getName(),
            'city' => $address->getCity(),
            'state' => $address->getRegion(),
            'zipCode' => $address->getPostcode(),
            'telNo' => $address->getTelephone(),
            'email' => $address->getEmail(),
            'country' => $address->getCountryId(),
            'residential' => $address->getCompany() ? 0 : 1,
            'vatNumber' => $address->getVatId() ?: '',
            'contact' => $address->getName(),
            'accountNumber' => '',
            'customerNumber' => '',
        ];

        if ($type) {
            $addressData['type'] = $type;
        }
        return $addressData + $streetFields;
    }

    /**
     * Collect shipping origin address data in SmartConnect format
     *
     * @param $store_id
     * @return array
     */
    private function getSenderData($store_id)
    {
        $magentoStreetLines = [
            $this->notPrivateScopeConfig->getValue(Shipment::XML_PATH_STORE_ADDRESS1, ScopeInterfaceAlias::SCOPE_STORES, $store_id),
            $this->notPrivateScopeConfig->getValue(Shipment::XML_PATH_STORE_ADDRESS2, ScopeInterfaceAlias::SCOPE_STORES, $store_id),
        ];

        list($streetName, $houseNo, $houseExt) = $this->notPrivateStreetParser->parseStreetAddress($magentoStreetLines);

        $streetLines = $this->notPrivateStreetParser->splitStreetLines($streetName);
        $senderData = [
            'type' => 'SEND',
            'name' => $this->notPrivateScopeConfig->getValue(Information::XML_PATH_STORE_INFO_NAME,ScopeInterfaceAlias::SCOPE_STORES,  $store_id),
            'city' => $this->notPrivateScopeConfig->getValue(Shipment::XML_PATH_STORE_CITY,ScopeInterfaceAlias::SCOPE_STORES,  $store_id),
            'telNo' => $this->notPrivateScopeConfig->getValue(Information::XML_PATH_STORE_INFO_PHONE,ScopeInterfaceAlias::SCOPE_STORES, $store_id),
            'houseNo' => $houseNo . $houseExt,
            'zipCode' => $this->notPrivateScopeConfig->getValue(Shipment::XML_PATH_STORE_ZIP,ScopeInterfaceAlias::SCOPE_STORES,  $store_id),
            'state' => $this->notPrivateScopeConfig->getValue(Shipment::XML_PATH_STORE_REGION_ID,ScopeInterfaceAlias::SCOPE_STORES,  $store_id),
            'country' => $this->notPrivateScopeConfig->getValue(Shipment::XML_PATH_STORE_COUNTRY_ID,ScopeInterfaceAlias::SCOPE_STORES, $store_id),
            'vatNumber' => $this->notPrivateScopeConfig->getValue(Information::XML_PATH_STORE_INFO_VAT_NUMBER,ScopeInterfaceAlias::SCOPE_STORES, $store_id) ?: '',
            'accountNumber' => '',
            'customerNumber' => '',
            'residential' => 0,
        ];


        return $senderData + $streetLines;
    }

}
