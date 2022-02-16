<?php
namespace CustomConcepts\Estimations\Block\Product\View;


use Magento\Framework\View\Element\Template;

class Stock extends \Magento\Catalog\Block\Product\View\Description
{
    /**
     * @var \CustomConcepts\Estimations\Helper\ShippingDate
     */
    protected $shippingDateHelper;
    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $productRepositoryInterface;
    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $_product;

    protected $_configurableProducingTime;

    protected $_simpleIds;

    protected $_dateOnStock;

    /**
     * Stock constructor.
     * @param Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \CustomConcepts\Estimations\Helper\ShippingDate $shippingDateHelper
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepositoryInterface
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \CustomConcepts\Estimations\Helper\ShippingDate $shippingDateHelper,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepositoryInterface,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        array $data = []
    ){
        $this->shippingDateHelper = $shippingDateHelper;
        $this->productRepositoryInterface = $productRepositoryInterface;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        parent::__construct($context, $registry, $data);
    }

    /**
     * @return string
     */
    public function _toHtml()
    {
        $this->loadProductData();

        $html = '';

        if($this->_dateOnStock){
            $html .= __($this->_product->getAttributeText('stock_status') . ' ' . $this->formatDate($this->_dateOnStock, \IntlDateFormatter::LONG)) . '<br/>';
        } else {
            $color = $this->_configurableProducingTime == 1 ? 'darkgreen' : 'orange';
            $html .= '<span style="color: ' . $color . '">'. __('shipment_text_producing_days_' . $this->_configurableProducingTime) .'</span><br/>';
        }

        foreach ($this->getOOSData() as $color => $info){
            if($info['oos_date']){
                $html .= '<span style="color: orange">';
                $html .= __('Color %1 is out of stock and will be shipped on %2.', $color, $info['oos_date']);
                $html .= '</span>';
            } elseif($info['producing_days']) {
                $color = $info['producing_days'] == 1 ? 'darkgreen' : 'orange';
                $html .= '<span style="color: ' . $color . '">';
                $html .= __('color_%1_shipment_text_producing_days_%2', $color, $info['producing_days']);
                $html .= '</span>';
            }
        }

        return $html;
    }

    public function loadProductData(){
        $this->_product = $this->getProduct();
        $this->_configurableProducingTime = $this->shippingDateHelper->getProducingDays($this->_product);
        $this->_simpleIds = $this->_product->getTypeInstance()->getChildrenIds($this->_product->getId());
        $this->_dateOnStock = $this->_product->getDateOnStock();
    }

    /**
     * @return array
     */
    public function getOOSData(){
        $OOSData = [];

        if(empty($this->_dateOnStock)){
            if($this->_simpleIds){
                $simpleProducts = $this->getSimpleProducts($this->_simpleIds);

                /** @var  $simpleProduct \Magento\Catalog\Model\Product */
                foreach ($simpleProducts as $simpleProduct){
                    $simpleProductProducingTime = $this->shippingDateHelper->getProducingDays($simpleProduct);
                    $simpleProductColor = strtolower($simpleProduct->getAttributeText('customproduct_color'));

                    if($simpleProductProducingTime > $this->_configurableProducingTime){
                        $OOSData[$simpleProductColor]['producing_days'] = $simpleProductProducingTime;
                    }
                    if($simpleProduct->getDateOnStock()){
                        $OOSData[$simpleProductColor]['oos_date'] = $this->formatDate($simpleProduct->getDateOnStock(), \IntlDateFormatter::LONG);
                    }
                }
            }
        }

        return $OOSData;
    }

    /**
     * @param $simpleIds
     * @return \Magento\Catalog\Api\Data\ProductInterface[]
     */
    public function getSimpleProducts($simpleIds){
        $this->searchCriteriaBuilder->addFilter('entity_id', $simpleIds, 'in');
        $this->searchCriteriaBuilder->addFilter('status', 1);

        $products = $this->productRepositoryInterface->getList($this->searchCriteriaBuilder->create());

        return $products->getItems();
    }

}
