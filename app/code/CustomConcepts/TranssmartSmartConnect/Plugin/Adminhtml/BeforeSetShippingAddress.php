<?php
namespace CustomConcepts\TranssmartSmartConnect\Plugin\Adminhtml;

class BeforeSetShippingAddress
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * BeforeSetShippingAddress constructor.
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request
    ){
        $this->request = $request;
    }

    /**
     * @param \Magento\Sales\Model\AdminOrder\Create $subject
     * @param $address
     * @return array
     */
    public function beforeSetShippingAddress(\Magento\Sales\Model\AdminOrder\Create $subject, $address){
        $order = $this->request->getPost('order');

        if(is_array($address)){
            if($pickupAddress = isset($order['pickup_address']) ? $order['pickup_address'] : false){
                $street = isset($pickupAddress['street_no']) ? $pickupAddress['street_no'] . ' ' : '';
                $street .= $pickupAddress['street'];

                $address['street'][0] = isset($pickupAddress['name']) ? $pickupAddress['name'] : '';
                $address['street'][1] = $street;
                $address['city'] = isset($pickupAddress['city']) ? $pickupAddress['city'] : '';
                $address['postcode'] = isset($pickupAddress['zipcode']) ? $pickupAddress['zipcode'] : '';
                $address['telephone'] = isset($pickupAddress['phone']) ? $pickupAddress['phone'] : $address['telephone'];

                //not existing in frontend
                $address['region'] = isset($pickupAddress['region']) ? $pickupAddress['region'] : '';
            }
        }

        return [$address];
    }
}
