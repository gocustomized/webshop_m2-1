<?php
declare(strict_types=1);

namespace CustomConcepts\FulfillmentDashboardIntegration\Model;

use Bluebirdday\TranssmartSmartConnect\Model\Data\Shipment;
use CustomConcepts\FulfillmentDashboardIntegration\Api\DashboardApiAdapterInterface;
use CustomConcepts\FulfillmentDashboardIntegration\Helper\Config;
use CustomConcepts\Giftsection\Helper\Data as GiftSectionData;
use CustomConcepts\GoCustomizer\Helper\Data as GCData;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Sales\Api\OrderItemRepositoryInterface;

class Adapter implements DashboardApiAdapterInterface
{
    /**
     * @var Curl
     */
    private $curl;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var string
     */
    private $body = '';

    /**
     * @var int
     */
    private $status = 0;

    protected $orderItemRepository;
    protected $gocustomizerHelper;
    /**
     * @var GiftSectionData
     */
    private $giftSectionHelper;

    /**
     * @param Curl $curl
     * @param Config $config
     * @param OrderItemRepositoryInterface $orderItemRepository
     * @param GCData $gocustomizerHelper
     * @param GiftSectionData $giftSectionHelper
     */
    public function __construct(
        Curl $curl,
        Config $config,
        OrderItemRepositoryInterface $orderItemRepository,
        GCData $gocustomizerHelper,
        GiftSectionData $giftSectionHelper
    ){
        $this->curl = $curl;
        $this->config = $config;
        $this->orderItemRepository = $orderItemRepository;
        $this->gocustomizerHelper = $gocustomizerHelper;
        $this->giftSectionHelper = $giftSectionHelper;
    }

    /**
     * @param Shipment[] $shipments
     * @return bool
     */
    public function sendShipmentsCreationRequest(array $shipments) : bool
    {
        $shipmentsData =[];
        /* @var Shipment $shipments **/
        foreach ($shipments as $transsmartShipment) {
            $transsmartShipmentArray = $transsmartShipment->toArray();
            $this->extendShipmentData($transsmartShipmentArray);
            $shipmentsData[] = $transsmartShipmentArray;
        }
        $url = $this->config->getDashboardDomain() . $this->config->getShipmentCreationPath();

        // set special header to skip 100:Continue status
        $this->curl->addHeader('Expect', '');

        $this->curl->post($url, $shipmentsData);
        $this->status = $this->curl->getStatus();
        $this->body = $this->curl->getBody();

        return $this->status === self::SUCCESS_SHIPMENT_CREATION_RESPONSE_STATUS;
    }

    /**
     * @return string
     */
    public function getResponse(): string
    {
        return $this->body;
    }

    /**
     * @return int
     */
    public function getStatusResponse(): int
    {
        return $this->status;
    }

    private function extendShipmentData(&$shipmentData){
        //set product thumbnail
        if(isset($shipmentData['deliveryNoteInformation']['deliveryNoteLines'])){
            foreach ($shipmentData['deliveryNoteInformation']['deliveryNoteLines'] as &$deliveryNoteLine){
                $item = $this->orderItemRepository->get($deliveryNoteLine['articleId']);
                $thumb = $this->gocustomizerHelper->getProductThumbnail($item);
                $gcdata = unserialize($item->getGocustomizerData());
                $notecardData = isset($gcdata['text'])? json_decode($gcdata['text'],true) : NULL;
                if($thumb){
                    $deliveryNoteLine['AssemblyInstructions'] = $thumb;
                }elseif($notecardData){
                    foreach($notecardData as &$data){
                        $search  = array('&'    ,  '<'   ,'>', '"');
                        $replace = array('&amp;', '&lt;', '&gt;', '&quot;');

                        str_replace($search,$replace,$data);
                    }
                    $notecardJson = json_encode(['notecardData' => $notecardData]);
                    $deliveryNoteLine['reasonOfExport'] = $notecardJson;
                }
            }
        }
    }
}
