<?php


namespace CustomConcepts\Estimations\Model\Data;

use CustomConcepts\Estimations\Api\Data\EstimationDatesInterface;

/**
 * Class EstimationDates
 *
 * @package CustomConcepts\Estimations\Model\Data
 */
class EstimationDates extends \Magento\Framework\Api\AbstractExtensibleObject implements EstimationDatesInterface
{

    /**
     * Get estimationdates_id
     * @return string|null
     */
    public function getEstimationdatesId()
    {
        return $this->_get(self::ESTIMATIONDATES_ID);
    }

    /**
     * Set estimationdates_id
     * @param string $estimationdatesId
     * @return \CustomConcepts\Estimations\Api\Data\EstimationDatesInterface
     */
    public function setEstimationdatesId($estimationdatesId)
    {
        return $this->setData(self::ESTIMATIONDATES_ID, $estimationdatesId);
    }

    /**
     * Get order_id
     * @return string|null
     */
    public function getOrderId()
    {
        return $this->_get(self::ORDER_ID);
    }

    /**
     * Set order_id
     * @param string $orderId
     * @return \CustomConcepts\Estimations\Api\Data\EstimationDatesInterface
     */
    public function setOrderId($orderId)
    {
        return $this->setData(self::ORDER_ID, $orderId);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \CustomConcepts\Estimations\Api\Data\EstimationDatesExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \CustomConcepts\Estimations\Api\Data\EstimationDatesExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \CustomConcepts\Estimations\Api\Data\EstimationDatesExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * Get order_ref
     * @return string|null
     */
    public function getOrderRef()
    {
        return $this->_get(self::ORDER_REF);
    }

    /**
     * Set order_ref
     * @param string $orderRef
     * @return \CustomConcepts\Estimations\Api\Data\EstimationDatesInterface
     */
    public function setOrderRef($orderRef)
    {
        return $this->setData(self::ORDER_REF, $orderRef);
    }

    /**
     * Get order_date
     * @return string|null
     */
    public function getOrderDate()
    {
        return $this->_get(self::ORDER_DATE);
    }

    /**
     * Set order_date
     * @param string $orderDate
     * @return \CustomConcepts\Estimations\Api\Data\EstimationDatesInterface
     */
    public function setOrderDate($orderDate)
    {
        return $this->setData(self::ORDER_DATE, $orderDate);
    }

    /**
     * Get shipping_date
     * @return string|null
     */
    public function getShippingDate()
    {
        return $this->_get(self::SHIPPING_DATE);
    }

    /**
     * Set shipping_date
     * @param string $shippingDate
     * @return \CustomConcepts\Estimations\Api\Data\EstimationDatesInterface
     */
    public function setShippingDate($shippingDate)
    {
        return $this->setData(self::SHIPPING_DATE, $shippingDate);
    }

    /**
     * Get min_delivery_date
     * @return string|null
     */
    public function getMinDeliveryDate()
    {
        return $this->_get(self::MIN_DELIVERY_DATE);
    }

    /**
     * Set min_delivery_date
     * @param string $minDeliveryDate
     * @return \CustomConcepts\Estimations\Api\Data\EstimationDatesInterface
     */
    public function setMinDeliveryDate($minDeliveryDate)
    {
        return $this->setData(self::MIN_DELIVERY_DATE, $minDeliveryDate);
    }

    /**
     * Get max_delivery_date
     * @return string|null
     */
    public function getMaxDeliveryDate()
    {
        return $this->_get(self::MAX_DELIVERY_DATE);
    }

    /**
     * Set max_delivery_date
     * @param string $maxDeliveryDate
     * @return \CustomConcepts\Estimations\Api\Data\EstimationDatesInterface
     */
    public function setMaxDeliveryDate($maxDeliveryDate)
    {
        return $this->setData(self::MAX_DELIVERY_DATE, $maxDeliveryDate);
    }

    /**
     * Get delivery_rate_id
     * @return string|null
     */
    public function getDeliveryRateId()
    {
        return $this->_get(self::DELIVERY_RATE_ID);
    }

    /**
     * Set delivery_rate_id
     * @param string $deliveryRateId
     * @return \CustomConcepts\Estimations\Api\Data\EstimationDatesInterface
     */
    public function setDeliveryRateId($deliveryRateId)
    {
        return $this->setData(self::DELIVERY_RATE_ID, $deliveryRateId);
    }

    /**
     * Get carrier_id
     * @return string|null
     */
    public function getCarrierId()
    {
        return $this->_get(self::CARRIER_ID);
    }

    /**
     * Set carrier_id
     * @param string $carrierId
     * @return \CustomConcepts\Estimations\Api\Data\EstimationDatesInterface
     */
    public function setCarrierId($carrierId)
    {
        return $this->setData(self::CARRIER_ID, $carrierId);
    }

    /**
     * Get country
     * @return string|null
     */
    public function getCountry()
    {
        return $this->_get(self::COUNTRY);
    }

    /**
     * Set country
     * @param string $country
     * @return \CustomConcepts\Estimations\Api\Data\EstimationDatesInterface
     */
    public function setCountry($country)
    {
        return $this->setData(self::COUNTRY, $country);
    }

    /**
     * Get created_at
     * @return string|null
     */
    public function getCreatedAt()
    {
        return $this->_get(self::CREATED_AT);
    }

    /**
     * Set created_at
     * @param string $createdAt
     * @return \CustomConcepts\Estimations\Api\Data\EstimationDatesInterface
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }
}

