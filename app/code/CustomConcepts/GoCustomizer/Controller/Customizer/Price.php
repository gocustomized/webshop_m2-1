<?php

/**
 * CustomConcepts_GoCustomizer extension
 * @category  CustomConcepts_Extensions
 * @package   CustomConcepts_GoCustomizer
 * @copyright Copyright (c) 2017
 * @author GoCustomized <info@gocustomized.com>
 */

namespace CustomConcepts\GoCustomizer\Controller\Customizer;

use Magento\Framework\App\Action\Context;

class Price extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $jsonResultFactory;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected $productCollection;
    /**
     * @var \Magento\Directory\Model\CurrencyFactory
     */
    protected $currencyFactory;


    /**
     * Price constructor.
     * @param Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection
     * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory
    )
    {
        parent::__construct($context);
        $this->jsonResultFactory = $jsonResultFactory;
        $this->storeManager = $storeManager;
        $this->productCollection = $productCollection;
        $this->currencyFactory = $currencyFactory;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        $result = $this->jsonResultFactory->create();
        $prod_prices = [];

        $products = $this->productCollection->addStoreFilter($this->storeManager->getStore()->getId())
                                            ->addAttributeToFilter('type_id', 'simple')
                                            ->addAttributeToFilter('attribute_set_id', ['in' => [11, 12]])
                                            ->addAttributeToSelect('sku')
                                            ->addAttributeToSelect('special_price')
                                            ->addAttributeToSelect('price')
                                            ->load();
        $currency_code = $this->storeManager->getStore()->getCurrentCurrencyCode();
        $prod_prices = [
            'currency' => $this->currencyFactory->create()->load($currency_code)->getCurrencySymbol()
        ];

        foreach($products as $product){
            if($product->getSpecialPrice()){
                $prod_prices[$product->getSku()] = $this->storeManager->getStore()->getCurrentCurrencyRate() * $product->getSpecialPrice();
            } else {
                $prod_prices[$product->getSku()] = $this->storeManager->getStore()->getCurrentCurrencyRate() * $product->getPrice();
            }
        }

        return $result->setData($prod_prices);
    }
}
