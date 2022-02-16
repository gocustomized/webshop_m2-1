<?php
declare(strict_types=1);

namespace CustomConcepts\TranssmartSmartConnectExtension\Plugin\Model\Shipment;

use Bluebirdday\TranssmartSmartConnect\Model\Data\AdditionalReferencesFactory;
use Bluebirdday\TranssmartSmartConnect\Model\Data\Shipment;
use CustomConcepts\TranssmartSmartConnectExtension\Helper\DirectoryHelper;
use Magento\Customer\Model\Group as CustomerGroup;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface as ScopeInterfaceAlias;

class Creator
{
    /**
     * @var DirectoryHelper
     */
    private $regionHelper;
    /**
     * @var AdditionalReferencesFactory
     */
    private $additionalReferencesFactory;
    /**
     * @var CustomerGroup
     */
    private $customerGroupCollection;
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    const XML_EORI_PATH = 'general/store_information/eori_number';

    /**
     * @param DirectoryHelper $regionHelper
     * @param AdditionalReferencesFactory $additionalReferencesFactory
     * @param CustomerGroup $customerGroupCollection
     * @param ScopeConfigInterface $scopeConfig
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(DirectoryHelper $regionHelper,
        AdditionalReferencesFactory $additionalReferencesFactory,
        CustomerGroup $customerGroupCollection,
        ScopeConfigInterface $scopeConfig,
        \Psr\Log\LoggerInterface $logger)
    {
        $this->regionHelper = $regionHelper;
        $this->additionalReferencesFactory = $additionalReferencesFactory;
        $this->customerGroupCollection = $customerGroupCollection;
        $this->scopeConfig = $scopeConfig;
        $this->logger = $logger;
    }

    /**
     * @param \Bluebirdday\TranssmartSmartConnectExtension\Model\Shipment\Creator $subject
     * @param Shipment $transsmartShipment
     * @return Shipment
     */
    public function afterCreateDataObject(
        \Bluebirdday\TranssmartSmartConnectExtension\Model\Shipment\Creator $subject,
        $shipmentData,
        $shipment
    ) {
        $country ='';
        $addresses = $shipmentData->getAddresses();
        foreach ($addresses as $address) {
            if ($regionName = $address->getState()
                && $regionCode = $this->regionHelper->getRegionCodeByName($address->getState())) {
                $address->setState($regionCode);
            }
            if ($address->getType() == 'SEND') {
                //@todo get it from the config
                // for now just the same as name
                $address->setContact($address->getName());
                $eoriNumber = ($this->scopeConfig->getValue(SELF::XML_EORI_PATH,ScopeInterfaceAlias::SCOPE_STORES,$shipment->getOrder()->getStore()->getId()) ?: null);
                if ($eoriNumber) {
                    $address->setData('eoriNumber', $eoriNumber);
                }
            }
            if ($address->getType() == 'INVC') {
                $country = $address->getCountry();

                // @todo verify with the Transsmart support. For now just copied from the M1
                if ($country == 'US') {
                    $shipmentData->setData('incoterms', 'DDP');
                }
            }
        }

        $shipmentData = $this->addAdditionalData($shipmentData);

        $shipmentData = $this->addAdditionalReferences($shipmentData,$shipment);

        $shipmentData = $this->convertZeroItems($shipmentData);

        return $shipmentData;
    }

    /**
     * convert price for items with 0 prices
     * @param $shipmentData
     * @return mixed
     */
    private function convertZeroItems($shipmentData){
        $deliveryNoteInformationTemp = $shipmentData->getData('deliveryNoteInformation');

        $hasZeroItems = false;
        $subTotal = 0;
        foreach ($deliveryNoteInformationTemp['deliveryNoteLines'] as &$deliveryNoteLine){
            if($deliveryNoteLine['price'] <= 0){
                $deliveryNoteLine['price'] = 5.00 * $deliveryNoteLine['quantity'];
                $hasZeroItems = true;
            }
            $subTotal += $deliveryNoteLine['price'];
        }

        if($hasZeroItems){
            $shipmentData->setData('deliveryNoteInformation', $deliveryNoteInformationTemp);
            $shipmentData->setData('value', $subTotal);
        }

        return $shipmentData;
    }

    private function addAdditionalData($shipmentData){
        $deliveryNoteInformationTemp = $shipmentData->getData('deliveryNoteInformation');

        foreach ($deliveryNoteInformationTemp['deliveryNoteLines'] as &$deliveryNoteLine){
            if(!isset($deliveryNoteLine['countryOrigin'])){
                $deliveryNoteLine['countryOrigin'] = 'NL';
            }
            if(!isset($deliveryNoteLine['grossWeight'])){
                $deliveryNoteLine['grossWeight'] = 0.01;
            }
            if(!isset($deliveryNoteLine['nettWeight'])){
                $deliveryNoteLine['nettWeight'] = 0.01;
            }
        }

        $shipmentData->setData('deliveryNoteInformation', $deliveryNoteInformationTemp);

        return $shipmentData;
    }


    public function addAdditionalReferences($shipmentData, $shipment){
        /** @var \Bluebirdday\TranssmartSmartConnect\Model\Data\AdditionalReferences $additionalReferences */
        $additionalReferences = $this->additionalReferencesFactory->create();

        $order = $shipment->getOrder();
        $additionalReferences->addReference('ORDER', $order->getIncrementId());

        /* Extra additional references used for fulfillment dashboard */
        $customerGroupId = $order->getCustomerGroupId();
        $groupCollection = $this->customerGroupCollection->load($customerGroupId);
        $groupCode       = $groupCollection->getCustomerGroupCode();

        $additionalReferences->addReference('AGENT', $this->scopeConfig->getValue('transsmart_shipping/apiparam/color_code',ScopeInterfaceAlias::SCOPE_STORES, $order->getStore()->getId()));  //transsmart_shipping/apiparam/color_code
        $additionalReferences->addReference('OTHER', $groupCode);
        $additionalReferences->addReference('YOURREFERENCE', $this->scopeConfig->getValue('general/store_information/name',ScopeInterfaceAlias::SCOPE_STORES, $order->getStore()->getId()));
        /* Extra additional references used for fulfillment dashboard */

        if ($shipment->getShippingAddress()->getTranssmartServicePointId()) {
            $additionalReferences->addReference(
                'SERVICEPOINT',
                $shipment->getShippingAddress()->getTranssmartServicePointId()
            );
        }
        $shipmentData->setData('additionalReferences', $additionalReferences);
        return $shipmentData;
    }




}
