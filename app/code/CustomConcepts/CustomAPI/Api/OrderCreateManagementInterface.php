<?php


namespace CustomConcepts\CustomAPI\Api;

interface OrderCreateManagementInterface
{


    /**
     * GET for CartCreate api
     * @param string 
     * @return string
     */
    public function createApiOrder($data);

    /**
     * Process quoteitem to save the customizer data
     * @param string 
     * @return string
     */
    public function processCustomizerItem($customizerItem);
    
    /**
     * Check for new order 
     * @param string 
     * @return bool
     */
    public function verifyNewOrder($apiOrderId);
    
    /**
     * Assign a specified customer to a specified shopping cart.
     *
     * @param string $quoteId The cart ID.
     * @param int $customerId The customer ID.
     * @param int $storeId
     * @return string
     */
    public function assignCartCustomer($quoteId, $customerId, $storeId);
    
    /**
     * Assign an order API number to newly created order.
     *
     * @param string $apiOrderId API Order ID.
     * @param int $orderId The Order ID.
     * @return string
     */
    public function addAPIOrderId($apiOrderId, $orderId);
}