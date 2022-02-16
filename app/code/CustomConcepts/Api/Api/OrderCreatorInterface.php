<?php
namespace CustomConcepts\Api\Api;

/**
 * Interface OrderCreatorInterface
 */
interface OrderCreatorInterface
{
    /**
     * @return mixed
     */
    public function createOrder();

    /**
     * @return mixed
     */
    public function createOrders();
}
