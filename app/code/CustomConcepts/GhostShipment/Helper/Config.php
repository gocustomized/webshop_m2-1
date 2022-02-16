<?php
declare(strict_types=1);

namespace CustomConcepts\GhostShipment\Helper;

use Bluebirdday\TranssmartSmartConnect\Model\CostcenterRepository;
use Bluebirdday\TranssmartSmartConnect\Model\IncotermRepository;
use Bluebirdday\TranssmartSmartConnect\Model\Mail\TypeRepository;
use Bluebirdday\TranssmartSmartConnect\Model\PackageRepository;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Bluebirdday\TranssmartSmartConnect\Model\Mail\TypeManagement;
use Bluebirdday\TranssmartSmartConnect\Model\IncotermManagement;
use Bluebirdday\TranssmartSmartConnect\Model\CostcenterManagement;
use Bluebirdday\TranssmartSmartConnect\Model\PackageManagement;

class Config extends AbstractHelper
{
    const XML_PATH_ENABLE_GHOST_SHIPMENT_CREATION = 'cc_ghost_shipment/general/enabled';
    const DEFAULT_BOOKING_PROFILE_CONFIG_PATH = 'transsmart/default_shipment_properties/booking_profile';

    /**
     * @var \Bluebirdday\TranssmartSmartConnect\Model\Booking\ProfileRepository
     */
    protected $profileRepository;
    /**
     * @var TypeRepository
     */
    protected $mailtypeRepository;
    /**
     * @var IncotermRepository
     */
    protected $incotermRepository;
    /**
     * @var CostcenterRepository
     */
    protected $costcenterRepository;
    /**
     * @var PackageRepository
     */
    protected $packageRepository;
    /**
     * @var TypeManagement
     */
    protected $typeManagement;
    /**
     * @var IncotermManagement
     */
    protected $incotermManagement;
    /**
     * @var CostcenterManagement
     */
    protected $costcenterManagement;
    /**
     * @var PackageManagement
     */
    protected $packageManagement;

    /**
     * Config constructor.
     * @param Context $context
     * @param \Bluebirdday\TranssmartSmartConnect\Model\Booking\ProfileRepository $profileRepository
     * @param TypeRepository $mailtypeRepository
     * @param IncotermRepository $incotermRepository
     * @param CostcenterRepository $costcenterRepository
     * @param PackageRepository $packageRepository
     * @param TypeManagement $typeManagement
     * @param IncotermManagement $incotermManagement
     * @param CostcenterManagement $costcenterManagement
     * @param PackageManagement $packageManagement
     */
    public function __construct(
        Context $context,
        \Bluebirdday\TranssmartSmartConnect\Model\Booking\ProfileRepository $profileRepository,
        TypeRepository $mailtypeRepository,
        IncotermRepository $incotermRepository,
        CostcenterRepository $costcenterRepository,
        PackageRepository $packageRepository,
        \Bluebirdday\TranssmartSmartConnect\Model\Mail\TypeManagement $typeManagement,
        \Bluebirdday\TranssmartSmartConnect\Model\IncotermManagement $incotermManagement,
        \Bluebirdday\TranssmartSmartConnect\Model\CostcenterManagement $costcenterManagement,
        \Bluebirdday\TranssmartSmartConnect\Model\PackageManagement  $packageManagement
    ){
        $this->profileRepository = $profileRepository;
        $this->mailtypeRepository = $mailtypeRepository;
        $this->incotermRepository = $incotermRepository;
        $this->costcenterRepository = $costcenterRepository;
        $this->packageRepository = $packageRepository;
        $this->typeManagement = $typeManagement;
        $this->incotermManagement = $incotermManagement;
        $this->costcenterManagement = $costcenterManagement;
        $this->packageManagement = $packageManagement;
        parent::__construct($context);
    }

    /**
     * @return bool
     */
    public function isGhostShipmentCreationEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLE_GHOST_SHIPMENT_CREATION,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @param $order
     * @return \Bluebirdday\TranssmartSmartConnect\Model\Booking\Profile|\Bluebirdday\TranssmartSmartConnect\Model\Profile|null
     */
    public function getDefaultBookingProfile($order){
        if($defaultProfileId = $this->getDefaultBookingProfileId($order)){
            return $this->profileRepository->load($defaultProfileId);
        } else {
            return null;
        }
    }

    /**
     * @param $order
     * @return \Bluebirdday\TranssmartSmartConnect\Model\Mail\Type|null
     */
    public function getDefaultEmailType($order){
        if($default = $this->getDefaultEmailTypeId($order)){
            if ($defaultEmailType = $this->mailtypeRepository->load($default)){
                return $defaultEmailType;
            }
        }
        return $this->typeManagement->getDefault();
    }

    /**
     * @param $order
     * @return \Bluebirdday\TranssmartSmartConnect\Model\Incoterm|null
     */
    public function getDefaultIncoterm($order){
        if($default = $this->getDefaultIncotermId($order)){
            if ($defaultIncoterm = $this->incotermRepository->load($default)){
                return $defaultIncoterm;
            }
        }
        return $this->incotermManagement->getDefault();
    }

    /**
     * @param $order
     * @return \Bluebirdday\TranssmartSmartConnect\Model\Costcenter|null
     */
    public function getDefaultCostCenter($order){
        if($default = $this->getDefaultCostCenterId($order)){
            if ($defaultCostCenter = $this->costcenterRepository->load($default)){
                return $defaultCostCenter;
            }
        }
        return $this->costcenterManagement->getDefault();
    }

    /**
     * @param $order
     * @return \Bluebirdday\TranssmartSmartConnect\Model\Package|false|null
     */
    public function getDefaultPackage($order){
        if($default = $this->getDefaultPackageId($order)){
            if ($defaultPackage = $this->packageRepository->loadByCode($default)){
                return $defaultPackage;
            }
        }
        return $this->packageManagement->getDefault();
    }

    /**
     * @param $order
     * @return mixed|null
     */
    public function getDefaultBookingProfileId($order){
        $storeId = $order->getStoreId();
        return $this->scopeConfig->getValue($this::DEFAULT_BOOKING_PROFILE_CONFIG_PATH, ScopeInterface::SCOPE_STORE, $storeId) ?: null;
    }

    /**
     * @param $order
     * @return mixed|null
     */
    public function getDefaultEmailTypeId($order){
        $storeId = $order->getStoreId();
        return $this->scopeConfig->getValue(TypeManagement::CONFIG_DEFAULT_PATH, ScopeInterface::SCOPE_STORE, $storeId) ?: null;
    }

    /**
     * @param $order
     * @return mixed|null
     */
    public function getDefaultIncotermId($order){
        $storeId = $order->getStoreId();
        return $this->scopeConfig->getValue(IncotermManagement::CONFIG_DEFAULT_PATH, ScopeInterface::SCOPE_STORE, $storeId) ?: null;
    }

    /**
     * @param $order
     * @return mixed|null
     */
    public function getDefaultCostCenterId($order){
        $storeId = $order->getStoreId();
        return $this->scopeConfig->getValue(CostcenterManagement::CONFIG_DEFAULT_PATH, ScopeInterface::SCOPE_STORE, $storeId) ?: null;
    }

    /**
     * @param $order
     * @return mixed|null
     */
    public function getDefaultPackageId($order){
        $storeId = $order->getStoreId();
        return $this->scopeConfig->getValue(PackageManagement::DEFAULT_PACKAGE_PATH, ScopeInterface::SCOPE_STORE, $storeId) ?: null;
    }
}
