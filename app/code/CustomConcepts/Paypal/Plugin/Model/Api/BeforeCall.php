<?php
namespace CustomConcepts\Paypal\Plugin\Model\Api;

use Magento\Store\Model\ScopeInterface;

class BeforeCall
{
    /**
     * @var \Magento\Directory\Helper\Data
     */
    protected $currencyHelper;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * BeforeCall constructor.
     * @param \Magento\Directory\Helper\Data $currencyHelper
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Directory\Helper\Data $currencyHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ){
        $this->currencyHelper = $currencyHelper;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }

    /**
     * @param \Magento\Paypal\Model\Api\Nvp $subject
     * @param $methodName
     * @param $request
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function beforeCall(\Magento\Paypal\Model\Api\Nvp $subject, $methodName, $request){
        if ($methodName == "SetExpressCheckout" || $methodName == "DoExpressCheckoutPayment") {
            $storeId = $this->storeManager->getStore()->getId();
            $baseCurrency = $this->scopeConfig->getValue('currency/options/base', ScopeInterface::SCOPE_STORE, $storeId);
            $defaultCurrency = $this->scopeConfig->getValue('currency/options/default', ScopeInterface::SCOPE_STORE, $storeId);

            if($baseCurrency != $defaultCurrency) { //use default currency for paypal checkout
                $conAmt = isset($request['AMT']) ? round($this->currencyHelper->currencyConvert($request['AMT'], $baseCurrency, $defaultCurrency), 2) : 0.00;
                $conShippingAmt = isset($request['SHIPPINGAMT']) ? round($this->currencyHelper->currencyConvert($request['SHIPPINGAMT'], $baseCurrency, $defaultCurrency), 2) : 0.00;
                $conItemAmt = isset($request['ITEMAMT']) ? round($this->currencyHelper->currencyConvert($request['ITEMAMT'], $baseCurrency, $defaultCurrency), 2) : 0.00;
                $conTaxAmt = isset($request['TAXAMT']) ? round($this->currencyHelper->currencyConvert($request['TAXAMT'], $baseCurrency, $defaultCurrency), 2) : 0.00;

                $request['CURRENCYCODE'] = $defaultCurrency;
                $request['AMT'] = $conAmt;
                $request['SHIPPINGAMT'] = $conShippingAmt;
                $request['ITEMAMT'] = $conItemAmt;
                $request['TAXAMT'] = $conTaxAmt;

                //for items total
                $itemCtr = 0;
                while(isset($request['L_AMT'.$itemCtr])){
                    $request['L_AMT'.$itemCtr] = round($this->currencyHelper->currencyConvert($request['L_AMT'.$itemCtr], $baseCurrency, $defaultCurrency), 2);
                    $itemCtr += 1;
                }
            }
        }

        return [$methodName, $request];
    }
}
