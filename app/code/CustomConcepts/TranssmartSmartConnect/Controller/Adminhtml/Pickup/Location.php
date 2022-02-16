<?php
declare(strict_types=1);

namespace CustomConcepts\TranssmartSmartConnect\Controller\Adminhtml\Pickup;

use Bluebirdday\TranssmartSmartConnect\Model\Booking\ProfileRepository;
use Bluebirdday\TranssmartSmartConnect\Model\CarrierRepository;
use Bluebirdday\TranssmartSmartConnect\Model\Location\Request\ValidatorFactory;
use Bluebirdday\TranssmartSmartConnect\Model\Location\RequestFactory;
use Bluebirdday\TranssmartSmartConnect\Model\Media\FileHandler;
use Bluebirdday\TranssmartSmartConnect\Model\Quote\AddressManager;
use Bluebirdday\TranssmartSmartConnect\Model\Rate\Data\Collector;
use Bluebirdday\TranssmartSmartConnect\Model\Street\Address\Parser;
use Bluebirdday\TranssmartSmartConnect\Model\Transsmart\Adapter;
use Magento\Backend\Model\Session\Quote;
use Magento\Directory\Helper\Data as DataHelper;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote\Address;
use Magento\Store\Model\Information;
use Psr\Log\LoggerInterface;

class Location extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'CustomConcepts_TranssmartSmartConnect::Locations';

    /**
     * @var \Bluebirdday\TranssmartSmartConnect\Model\Transsmart\Adapter
     */
    private $adapter;

    /**
     * @var Quote
     */
    private $sessionQuote;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Bluebirdday\TranssmartSmartConnect\Model\Media\FileHandler
     */
    private $fileHandler;

    /**
     * @var \Bluebirdday\TranssmartSmartConnect\Model\Location\Request\ValidatorFactory
     */
    private $validatorFactory;

    /**
     * @var \Bluebirdday\TranssmartSmartConnect\Model\Location\RequestFactory
     */
    private $locationRequestFactory;

    /**
     * @var \Bluebirdday\TranssmartSmartConnect\Model\Rate\Data\Collector
     */
    private $dataCollector;

    /**
     * @var \Bluebirdday\TranssmartSmartConnect\Model\Street\Address\Parser
     */
    private $streetParser;

    /**
     * @var \Bluebirdday\TranssmartSmartConnect\Model\Quote\AddressManager
     */
    private $addressManager;

    /**
     * @var \Bluebirdday\TranssmartSmartConnect\Model\Rate\Response\Parser
     */
    private $rateParser;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var ProfileRepository
     */
    private $profileRepository;

    /**
     * @var CarrierRepository
     */
    private $carrierRepository;

    /**
     * @var Address
     */
    private $destinationAddress;

    /**
     * @var \stdClass
     */
    private $currentProfile;

    public function __construct(
        Context $context,
        Quote $sessionQuote,
        Adapter $adapter,
        ScopeConfigInterface $scopeConfig,
        FileHandler $fileHandler,
        ValidatorFactory $validatorFactory,
        RequestFactory $locationRequestFactory,
        Collector $dataCollector,
        Parser $streetParser,
        AddressManager $addressManager,
        \Bluebirdday\TranssmartSmartConnect\Model\Rate\Response\Parser $rateParser,
        LoggerInterface $logger,
        ProfileRepository $profileRepository,
        CarrierRepository $carrierRepository
    ) {
        $this->sessionQuote = $sessionQuote;
        $this->scopeConfig = $scopeConfig;
        $this->adapter = $adapter;
        $this->fileHandler = $fileHandler;
        $this->validatorFactory = $validatorFactory;
        $this->locationRequestFactory = $locationRequestFactory;
        $this->dataCollector = $dataCollector;
        $this->streetParser = $streetParser;
        $this->addressManager = $addressManager;
        $this->rateParser = $rateParser;
        $this->logger = $logger;
        $this->profileRepository = $profileRepository;
        $this->carrierRepository = $carrierRepository;
        parent::__construct($context);
    }

    private function getErrorResponse($message)
    {
        $responseContent = new \stdClass();
        $responseContent->success = false;
        $responseContent->errorMessage = $message;
        return \json_encode($responseContent);
    }

    private function decodeAndSave($source, $propertyName, $prefix)
    {
        $fileUrl = '';

        if (property_exists($source, $propertyName) && !empty($source->$propertyName)) {
            $image = base64_decode($source->$propertyName);
            $imageFileName = $this->fileHandler->save($prefix . '_' . $propertyName, $image);
            $fileUrl = $this->fileHandler->getMediaUrl($imageFileName);
        }

        return $fileUrl;
    }

    public function execute()
    {
        $this->defineDestinationAddress();
        $this->defineCurrentProfile();

        $locationsRequestData = $this->prepareLocationRequest();
        $locationsRequest = $this->locationRequestFactory->create(['data' => $locationsRequestData]);
        $validator = $this->validatorFactory->create();
        if (!$validator->isValid($locationsRequest)) {
            $responseContent = $this->getErrorResponse('Invalid request: ' . $validator->getError());
            $this->getResponse()->setHttpResponseCode(400);
            return $this->getResponse()->representJson($responseContent);
        }

        try {
            $locations = $this->adapter->getPickupLocations($locationsRequest);
        } catch (LocalizedException $exception) {
            $this->logger->alert($exception->getMessage());
            $responseContent = $this->getErrorResponse($exception->getMessage());
            $this->getResponse()->setHttpResponseCode(400);
            return $this->getResponse()->representJson($responseContent);
        } catch (\RuntimeException $exception) {
            $this->logger->alert($exception->getMessage());
            $responseContent = $this->getErrorResponse(__('Cannot perform request. See log for details.'));
            $this->getResponse()->setHttpResponseCode(400);
            return $this->getResponse()->representJson($responseContent);
        }

        /** @var \stdClass[] $sources */
        $sources = $locations->sources;
        foreach ($sources as $sourceIndex => &$source) {
            // if we cannot name the files, we treat them as absent and log error
            if (!property_exists($source, 'name')) {
                $this->logger->error(
                    'Got empty source(carrier) name on Transsmart pickup locations request '
                    . $locationsRequest->__toString()
                );
                $source->logo = '';
                $source->marker = '';
                continue;
            }

            $carrierCode = $source->code;
            if ($carrierCode == $this->currentProfile->carrier) {
                $source->rates = [$this->currentProfile];
            } else {
                //when we don't have rates, we don't show this carrier locations
                unset($sources[$sourceIndex]);
                continue;
            }

            try {
                $source->logo = $this->decodeAndSave($source, 'logo', $carrierCode);
                $source->marker = $this->decodeAndSave($source, 'marker', $carrierCode);
            } catch (\Magento\Framework\Exception\FileSystemException $exception) {
                $this->logger->alert($exception->getMessage(), $exception->getTrace());
                $responseContent = $this->getErrorResponse('Cannot save file. See log for details.');
                $this->getResponse()->setHttpResponseCode(500);
                return $this->getResponse()->representJson($responseContent);
            }
        }

        return $this->getResponse()->representJson(json_encode($sources));
    }

    private function prepareLocationRequest() : array
    {
        $streetToParse = $this->destinationAddress->getStreet();
        // we use only first address line for location request. see TRANSM2-165
        $firstStreetLine = reset($streetToParse);
        $postCode = $this->destinationAddress->getPostcode();

        list($street, $houseNumber, $houseNumberExt) = $this->streetParser->parseStreetAddress([$firstStreetLine]);

        $merchantCountry = $this->scopeConfig->getValue(Information::XML_PATH_STORE_INFO_COUNTRY_CODE);
        $countryFrom = $merchantCountry ?: $this->scopeConfig->getValue(DataHelper::XML_PATH_DEFAULT_COUNTRY);

        return [
            'city' => $this->destinationAddress->getCity(),
            'zip_code' => $postCode,
            'street' => $street,
            'house_number' => $houseNumber,
            'house_number_ext' => $houseNumberExt,
            'country_from'=> $countryFrom,
            'country_to' => $this->destinationAddress->getCountryModel()->getData('iso2_code'),
            'provider' => $this->currentProfile->carrier,
            'limit' => '50',
        ];
    }

    private function defineCurrentProfile() : void
    {
        $this->currentProfile = new \stdClass();
        $this->currentProfile->carrier = '';
        $shippingMethod = $this->destinationAddress->getShippingMethod();
        if ($shippingMethod
            && ($shippingMethodParts = explode('_', $shippingMethod))
            && count($shippingMethodParts) === 3) {
            $profileCode = $shippingMethodParts[2];
            $profiles = $this->profileRepository->getByCodes([$profileCode]);
            foreach ($profiles as $profile) {
                $this->currentProfile->currency = $this->scopeConfig->getValue('currency/options/default');
                $this->currentProfile->carrier = $profile->getCarrierCode();
                $this->currentProfile->locationsAvailabitity = false;
                if ($carrier = $this->carrierRepository->loadByCode($profile->getCarrierCode())) {
                    $this->currentProfile->locationsAvailabitity = $carrier->getLocationSelect();
                }
                $this->currentProfile->profile = $profile->getCode();
                $this->currentProfile->salesPrice = (float) $this->destinationAddress->getShippingAmount();
                $this->currentProfile->description = $profile->getDescription();
            }
        }
    }

    private function defineDestinationAddress() : void
    {
        $quote = $this->sessionQuote->getQuote();

        $this->destinationAddress = $quote->getShippingAddress();
    }
}
