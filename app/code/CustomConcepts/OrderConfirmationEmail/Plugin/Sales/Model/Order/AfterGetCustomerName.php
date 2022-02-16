<?php
namespace CustomConcepts\OrderConfirmationEmail\Plugin\Sales\Model\Order;


use Magento\Config\Model\Config\Source\Nooptreq;
use Magento\Store\Model\ScopeInterface;

class AfterGetCustomerName
{
    protected $customerRepository;
    protected $scopeConfig;

    public function __construct(
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ){
        $this->customerRepository = $customerRepository;
        $this->scopeConfig = $scopeConfig;
    }

    public function afterGetCustomerName(\Magento\Sales\Model\Order $subject, $result){
        if($result == (string)__('Guest')){
            $customerId = $subject->getCustomerId();
            if($customerId && !$subject->getCustomerIsGuest()){
                /** @var \Magento\Customer\Api\Data\CustomerInterface $customer */
                $customer = $this->customerRepository->getById($customerId);

                $customerName = '';
                if ($this->isVisibleCustomerPrefix() && strlen($customer->getSuffix())) {
                    $customerName .= $customer->getSuffix() . ' ';
                }
                $customerName .= $customer->getFirstname();
                if ($this->isVisibleCustomerMiddlename() && strlen($customer->getMiddlename())) {
                    $customerName .= ' ' . $customer->getMiddlename();
                }
                $customerName .= ' ' . $customer->getLastname();
                if ($this->isVisibleCustomerSuffix() && strlen($customer->getSuffix())) {
                    $customerName .= ' ' . $customer->getSuffix();
                }

                return $customerName;
            }
        }

        return $result;
    }

    private function isVisibleCustomerPrefix(): bool
    {
        $prefixShowValue = $this->scopeConfig->getValue(
            'customer/address/prefix_show',
            ScopeInterface::SCOPE_STORE
        );

        return $prefixShowValue !== Nooptreq::VALUE_NO;
    }

    private function isVisibleCustomerMiddlename(): bool
    {
        return $this->scopeConfig->isSetFlag(
            'customer/address/middlename_show',
            ScopeInterface::SCOPE_STORE
        );
    }

    private function isVisibleCustomerSuffix(): bool
    {
        $prefixShowValue = $this->scopeConfig->getValue(
            'customer/address/suffix_show',
            ScopeInterface::SCOPE_STORE
        );

        return $prefixShowValue !== Nooptreq::VALUE_NO;
    }
}
