<?php
namespace Mendz\Test\Controller\Adminhtml\Test;

use Magento\Backend\App\Action\Context;
use Magento\Store\Model\ScopeInterface;

class Test extends \Magento\Backend\App\Action
{
    protected $xtentoHelper;
    protected $estimationsHelper;
    protected $orderRepository;
    protected $estimations;
    protected $storeManager;
    protected $scopeConfig;

    public function __construct(
        Context $context,
        \CustomConcepts\XtentoOrderExport\Helper\Data $xtentoHelper,
        \CustomConcepts\Estimations\Helper\Data $estimationsHelper,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \CustomConcepts\Estimations\Helper\Estimation $estimations,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ){
        $this->xtentoHelper = $xtentoHelper;
        $this->estimationsHelper = $estimationsHelper;
        $this->orderRepository = $orderRepository;
        $this->estimations = $estimations;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context);
    }

    public function execute()
    {
        $storeId = $this->storeManager->getStore()->getId();
//        $value = $this->scopeConfig->getValue('transsmart/default_shipment_properties/incoterm', ScopeInterface::SCOPE_STORE, $storeId);
        $value = $this->scopeConfig->getValue('transsmart/default_shipment_properties/incoterm');

        die($value);
    }
}
