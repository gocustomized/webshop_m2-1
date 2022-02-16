<?php
declare(strict_types=1);

namespace CustomConcepts\GhostShipment\Service;

use CustomConcepts\GhostShipment\Api\TranssmartShipmentFieldGeneratorInterface;
use Magento\Framework\DataObject;
use Magento\Sales\Api\Data\OrderInterface;
use Bluebirdday\TranssmartSmartConnect\Model\Booking\Profile;
use Bluebirdday\TranssmartSmartConnect\Model\CostcenterManagement;
use Bluebirdday\TranssmartSmartConnect\Model\IncotermManagement;
use Bluebirdday\TranssmartSmartConnect\Model\Mail\TypeManagement as MailTypeManagement;
use Bluebirdday\TranssmartSmartConnect\Model\PackageManagement;
use Bluebirdday\TranssmartSmartConnect\Model\Shipping\Method\Parser;
use CustomConcepts\GhostShipment\Api\GhostShipmentCreatorInterface;
use CustomConcepts\GhostShipment\Api\GhostShipmentExceptionHandlerInterface;
use CustomConcepts\GhostShipment\Helper\Config;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Convert\Order as OrderConverter;

class TranssmartShipmentFieldGenerator implements TranssmartShipmentFieldGeneratorInterface
{
    /**
     * @var Json
     */
    private $jsonHelper;

    /**
     * @var PackageManagement
     */
    private $packageManagement;

    /**
     * @var CostcenterManagement
     */
    private $costcenterManagement;

    /**
     * @var IncotermManagement
     */
    private $incotermManagement;

    /**
     * @var MailTypeManagement
     */
    private $mailTypeManagement;

    /**
     * @var Parser
     */
    private $shippingMethodParser;

    /**
     * @var Config
     */
    protected $ghostShipmentConfigHelper;

    /**
     * @param Json $jsonHelper
     * @param PackageManagement $packageManagement
     * @param CostcenterManagement $costcenterManagement
     * @param IncotermManagement $incotermManagement
     * @param MailTypeManagement $mailTypeManagement
     * @param Parser $shippingMethodParser
     */
    public function __construct(
        Json $jsonHelper,
        PackageManagement $packageManagement,
        CostcenterManagement $costcenterManagement,
        IncotermManagement $incotermManagement,
        MailTypeManagement $mailTypeManagement,
        Parser $shippingMethodParser,
        \CustomConcepts\GhostShipment\Helper\Config $ghostShipmentConfigHelper
    ) {
        $this->jsonHelper = $jsonHelper;
        $this->packageManagement = $packageManagement;
        $this->costcenterManagement = $costcenterManagement;
        $this->incotermManagement = $incotermManagement;
        $this->mailTypeManagement = $mailTypeManagement;
        $this->shippingMethodParser = $shippingMethodParser;
        $this->ghostShipmentConfigHelper = $ghostShipmentConfigHelper;
    }

    /**
     * @inheritDoc
     */
    public function generate(OrderInterface $order): DataObject
    {
        $transsmartFields = new DataObject();
        $bookingProfile = $this->shippingMethodParser->parseOrder($order) ?: $this->ghostShipmentConfigHelper->getDefaultBookingProfile($order);
        if (!$bookingProfile) {
            throw new LocalizedException(__('Cannot find booking profiler for order %1', $order->getIncrementId()));
        }

        $transsmartFields->setData('transsmart_book', true);

        $costCenterId = $bookingProfile->getCostcenterId();
        if (!$costCenterId) {
            $defaultCostcenter = $this->ghostShipmentConfigHelper->getDefaultCostCenter($order);
            if (!$defaultCostcenter) {
                throw new LocalizedException(__('Cannot find cost center for order %1', $order->getIncrementId()));
            }
            $costCenterId = $defaultCostcenter->getId();
        }

        $defaultPackage = $this->ghostShipmentConfigHelper->getDefaultPackage($order);
        if (!$defaultPackage) {
            throw new LocalizedException(__('Default package type not found'));
        }

        $incotermId = $bookingProfile->getIncotermId();
        if (!$incotermId) {
            $defaultIncoterm = $this->ghostShipmentConfigHelper->getDefaultIncoterm($order);
            if (!$defaultIncoterm) {
                throw new LocalizedException(__('Default incoterm not found'));
            }
            $incotermId = $defaultIncoterm->getId();
        }

        $emailTypeId = $bookingProfile->getMailTypeId();
        if (!$emailTypeId) {
            $defaultMailType = $this->ghostShipmentConfigHelper->getDefaultEmailType($order);
            if (!$defaultMailType) {
                throw new LocalizedException(__('Default mailtype not found'));
            }
            $emailTypeId = $defaultMailType->getId();
        }

        $transsmartFields->setData('transsmart_bookingprofile_id', $bookingProfile->getId());
        $transsmartFields->setData('transsmart_costcenter_id', $costCenterId);
        $transsmartFields->setData('transsmart_emailtype_id', $emailTypeId);
        $transsmartFields->setData('transsmart_incoterm_id', $incotermId);

        $packages = [
            1 => [
                'type' => $defaultPackage->getCode(),
                'qty' => '1',
                'length' => $defaultPackage->getLength(),
                'width' => $defaultPackage->getWidth(),
                'height' => $defaultPackage->getHeight(),
                'weight' => $defaultPackage->getWeight(),
            ]
        ];
        $transsmartFields->setData('transsmart_packages', $this->jsonHelper->serialize($packages));

        return $transsmartFields;
    }
}
