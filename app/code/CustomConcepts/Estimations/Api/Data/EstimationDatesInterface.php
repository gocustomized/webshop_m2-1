<?php


namespace CustomConcepts\Estimations\Api\Data;

/**
 * Interface EstimationDatesInterface
 *
 * @package CustomConcepts\Estimations\Api\Data
 */
interface EstimationDatesInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    const ESTIMATIONDATES_ID = 'estimationdates_id';
    const ORDER_ID = 'order_id';
    const ORDER_REF = 'order_ref';
    const SHIPPING_DATE = 'shipping_date';
    const ORDER_DATE = 'order_date';
    const MAX_DELIVERY_DATE = 'max_delivery_date';
    const CARRIER_ID = 'carrier_id';
    const DELIVERY_RATE_ID = 'delivery_rate_id';
    const MIN_DELIVERY_DATE = 'min_delivery_date';
    const COUNTRY = 'country';
    const CREATED_AT = 'created_at';

    /**
     * Get estimationdates_id
     * @return string|null
     */
    public function getEstimationdatesId();

    /**
     * Set estimationdates_id
     * @param string $estimationdatesId
     * @return \CustomConcepts\Estimations\Api\Data\EstimationDatesInterface
     */
    public function setEstimationdatesId($estimationdatesId);

    /**
     * Get order_id
     * @return string|null
     */
    public function getOrderId();

    /**
     * Set order_id
     * @param string $orderId
     * @return \CustomConcepts\Estimations\Api\Data\EstimationDatesInterface
     */
    public function setOrderId($orderId);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \CustomConcepts\Estimations\Api\Data\EstimationDatesExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \CustomConcepts\Estimations\Api\Data\EstimationDatesExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \CustomConcepts\Estimations\Api\Data\EstimationDatesExtensionInterface $extensionAttributes
    );

    /**
     * Get order_ref
     * @return string|null
     */
    public function getOrderRef();

    /**
     * Set order_ref
     * @param string $orderRef
     * @return \CustomConcepts\Estimations\Api\Data\EstimationDatesInterface
     */
    public function setOrderRef($orderRef);

    /**
     * Get order_date
     * @return string|null
     */
    public function getOrderDate();

    /**
     * Set order_date
     * @param string $orderDate
     * @return \CustomConcepts\Estimations\Api\Data\EstimationDatesInterface
     */
    public function setOrderDate($orderDate);

    /**
     * Get shipping_date
     * @return string|null
     */
    public function getShippingDate();

    /**
     * Set shipping_date
     * @param string $shippingDate
     * @return \CustomConcepts\Estimations\Api\Data\EstimationDatesInterface
     */
    public function setShippingDate($shippingDate);

    /**
     * Get min_delivery_date
     * @return string|null
     */
    public function getMinDeliveryDate();

    /**
     * Set min_delivery_date
     * @param string $minDeliveryDate
     * @return \CustomConcepts\Estimations\Api\Data\EstimationDatesInterface
     */
    public function setMinDeliveryDate($minDeliveryDate);

    /**
     * Get max_delivery_date
     * @return string|null
     */
    public function getMaxDeliveryDate();

    /**
     * Set max_delivery_date
     * @param string $maxDeliveryDate
     * @return \CustomConcepts\Estimations\Api\Data\EstimationDatesInterface
     */
    public function setMaxDeliveryDate($maxDeliveryDate);

    /**
     * Get delivery_rate_id
     * @return string|null
     */
    public function getDeliveryRateId();

    /**
     * Set delivery_rate_id
     * @param string $deliveryRateId
     * @return \CustomConcepts\Estimations\Api\Data\EstimationDatesInterface
     */
    public function setDeliveryRateId($deliveryRateId);

    /**
     * Get carrier_id
     * @return string|null
     */
    public function getCarrierId();

    /**
     * Set carrier_id
     * @param string $carrierId
     * @return \CustomConcepts\Estimations\Api\Data\EstimationDatesInterface
     */
    public function setCarrierId($carrierId);

    /**
     * Get country
     * @return string|null
     */
    public function getCountry();

    /**
     * Set country
     * @param string $country
     * @return \CustomConcepts\Estimations\Api\Data\EstimationDatesInterface
     */
    public function setCountry($country);

    /**
     * Get created_at
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set created_at
     * @param string $createdAt
     * @return \CustomConcepts\Estimations\Api\Data\EstimationDatesInterface
     */
    public function setCreatedAt($createdAt);
}

