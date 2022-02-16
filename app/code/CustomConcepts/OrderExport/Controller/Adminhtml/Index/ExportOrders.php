<?php

/**
 * CustomConcepts_OrderExport extension
 * @category  CustomConcepts_Extensions
 * @package   CustomConcepts_OrderExport
 * @copyright Copyright (c) 2017
 * @author GoCustomized <info@gocustomized.com>
 */

namespace CustomConcepts\OrderExport\Controller\Adminhtml\Index;

use Magento\Framework\Controller\ResultFactory;

class ExportOrders extends \Magento\Backend\App\Action {

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $fileFactory;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $_orderCollectionFactory;

    /**
     * Price Helper
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $priceHelper;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Pricing\Helper\Data $priceHelper
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     * 
     */
    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\Pricing\Helper\Data $priceHelper, \Magento\Framework\App\Response\Http\FileFactory $fileFactory, \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
    ) {
        $this->fileFactory = $fileFactory;
        $this->_orderCollectionFactory = $orderCollectionFactory;
        $this->messageManager = $context->getMessageManager();
        $this->priceHelper = $priceHelper;
        parent::__construct($context);
    }

    /*
     * ACL check
     */

    public function _isAllowed() {
        return $this->_authorization->isAllowed('CustomConcepts_OrderExport::orderexport');
    }

    /**
     * @return XLS file
     */
    public function execute() {
        //get POST Data
        $postData = $this->getRequest()->getParams();

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if (!isset($postData['namespace']) && (empty($postData['from_date']) || empty($postData['to_date']) || empty($postData['stores']) || empty($postData['supplier']) || empty($postData['order_statuses']))) {
            $this->messageManager->addError(__('Please select all required values in form'));
            $resultRedirect->setPath('*/*/');
            return $resultRedirect;
        }
        //check if its a mass action or not
        if (!isset($postData['namespace'])) {
            $fromDate = date('Y-m-d H:i:s', strtotime($postData['from_date']));
            $toDate = date('Y-m-d H:i:s', strtotime($postData['to_date']));

            //add condition to validate for days not greater then  1 month
            $dtTo = new \DateTime($toDate);
            $daysBetween = $dtTo->diff(new \DateTime($fromDate))->days;
            if ($daysBetween >= 32) {
                $this->messageManager->addError(__('Please select a date range not greater then 1 month.'));
                $resultRedirect->setPath('*/*/');
                return $resultRedirect;
            }
        }
        //get order collection with required fields
        $collection = $this->_orderCollectionFactory->create()
                ->addFieldToSelect([
            'increment_id', 'ext_order_id', 'created_at', 'status', 'order_note', 'shipping_description', 'store_name', 'store_id'
        ]);

        // You Can filter collection for date ranges 
        if (!isset($postData['namespace']) && !empty($fromDate) && !empty($toDate)) {
            $collection->addAttributeToFilter('main_table.created_at', array('from' => $fromDate, 'to' => $toDate))
                    ->addAttributeToFilter('main_table.status', array('in' => array($postData['order_statuses'])));
        }

        // You Can filter for specific customer
        if (!isset($postData['namespace']) && !empty($postData['client_id'])) {
            $collection->addFieldToFilter('main_table.customer_id', $postData['client_id']);
        }

        // When mass selected order ids passed
        if (isset($postData['namespace']) && !empty($postData['selected'])) {
            $collection->addFieldToFilter('main_table.entity_id', array('in' => array($postData['selected'])));
        }

        // You Can filter for specific store
        $allstores = false;

        if (isset($postData['namespace']) || in_array('0', $postData['stores'])) {
            $allstores = true;
        }
        if ($allstores == false) {
            $collection->addFieldToFilter('main_table.store_id', array('in' => array($postData['stores'])));
        }

        //join with item table
        $collection->getSelect()->join(
                array('dropshipitem' => 'sales_order_item'), 'dropshipitem.order_id=main_table.entity_id', ['item_id', 'qty_ordered', 'export_data', 'product_id', 'sku', 'row_total']);
        //join for supplier
        $collection->getSelect()->joinLeft(
                array('option' => 'eav_attribute_option_value'), 'dropshipitem.supplier_id=`option`.option_id', ['supplier' => 'value']);
        //join for get address info
        $collection->getSelect()->joinLeft(
                array('address' => 'sales_order_address'), 'address.parent_id=main_table.entity_id AND address.address_type="shipping"', ['country' => 'country_id']);

        // You Can filter for specific suppliers
        if (!isset($postData['namespace']) && count($postData['supplier']) > 0) {
            $collection->addFieldToFilter('dropshipitem.supplier_id', array('in' => array($postData['supplier'])));
        }

        $collection->getSelect()->distinct();
        $collection->getSelect()->group('main_table.entity_id')->order('item_id', 'ASC');

        //check is there any orders or not
        $size = $collection->getSize();

        if (!$size || empty($size)) {
            $this->messageManager->addError(__('No orders available with this selection.'));
            $resultRedirect->setPath('*/*/');
            return $resultRedirect;
        }
        //column heades
        $row[] = [
            __("Order number"),
            __("Order status"),
            __("Order date"),
            __("Ext. ref. order"),
            __("Shipping method"),
            __("Shipping country"),
            __("Storeview"),
            __("SKU"),
            __("Product id"),
            __("Item qty"),
            __("Dropship id"),
            __("Supplier"),
            __("Row total"),
            __("Order Note")
        ];

        //column values
        foreach ($collection->getItems() as $orderItem) {

            $row[] = [
                $orderItem->getIncrementId(),
                $orderItem->getStatus(),
                $orderItem->getCreatedAt(),
                $orderItem->getExtOrderId(),
                $orderItem->getShippingDescription(),
                $orderItem->getCountry(),
                $orderItem->getStoreName(),
                $orderItem->getSku(),
                $orderItem->getProductId(),
                $orderItem->getQtyOrdered(),
                $orderItem->getIncrementId() . $orderItem->getItemId(),
                $orderItem->getSupplier(),
                $this->priceHelper->currencyByStore($orderItem->getRowTotal(), $orderItem->getStoreId(), true, false),
                $orderItem->getOrderNote()
            ];
        }

        //generate xls file using data
        $convert = new \Magento\Framework\Convert\Excel(new \ArrayIterator($row));
        $content = $convert->convert('single_sheet');
        return $this->fileFactory->create("download_orders/downloads_" . date('Ymd_His') . ".xls", $content, \Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR);
    }

}
