<?php
namespace CustomConcepts\Estimations\Model\EstimationDates;


use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;

class DataProvider extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider
{
    protected $loadedData;
    protected $profileRepository;
    protected $carrierRepository;
    protected $timezone;

    public function __construct(
        $name, $primaryFieldName,
        $requestFieldName,
        ReportingInterface $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        \Bluebirdday\TranssmartSmartConnect\Model\Booking\ProfileRepository $profileRepository,
        \Bluebirdday\TranssmartSmartConnect\Model\CarrierRepository $carrierRepository,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        array $meta = [],
        array $data = []
    ){
        $this->profileRepository = $profileRepository;
        $this->carrierRepository = $carrierRepository;
        $this->timezone = $timezone;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $reporting, $searchCriteriaBuilder, $request, $filterBuilder, $meta, $data);
    }

    public function getData()
    {
        if($this->loadedData){
            return $this->loadedData;
        }

        $this->loadedData = parent::getData();
        foreach ($this->loadedData['items'] as &$item){
            if(isset($item['carrier_id'])){
                $profile = $this->profileRepository->loadByCode($item['carrier_id']);
                $carrier = $this->carrierRepository->load($profile->getCarrierId());
                $item['carrier'] = $profile->getCode() . ' - ' . $carrier->getCode();
            }

            $item['shipping_date'] = $this->timezone->formatDate($item['shipping_date'], \IntlDateFormatter::FULL, false);
            $item['min_delivery_date'] = $this->timezone->formatDate($item['min_delivery_date'], \IntlDateFormatter::FULL, false);
            $item['max_delivery_date'] = $this->timezone->formatDate($item['max_delivery_date'], \IntlDateFormatter::FULL, false);
            $item['created_at'] = $this->timezone->formatDate($item['created_at'], \IntlDateFormatter::FULL, false);
        }

        return $this->loadedData;
    }
}
