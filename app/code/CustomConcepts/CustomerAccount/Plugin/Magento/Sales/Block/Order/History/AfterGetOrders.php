<?php
namespace CustomConcepts\CustomerAccount\Plugin\Magento\Sales\Block\Order\History;

class AfterGetOrders extends \Magento\Sales\Block\Order\History
{
    /**
     * @param \Magento\Sales\Block\Order\History $subject
     * @param $result
     * @return mixed
     */
    public function afterGetOrders(\Magento\Sales\Block\Order\History $subject, $result){
        if($orderNo = $this->getRequest()->getParam('order_search')){
            $result->addFieldToFilter('increment_id', $orderNo);
        }
        return $result;
    }
}
