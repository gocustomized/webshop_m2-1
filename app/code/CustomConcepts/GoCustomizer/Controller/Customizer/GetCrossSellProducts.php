<?php
namespace CustomConcepts\GoCustomizer\Controller\Customizer;

use Magento\Framework\App\Action\Context;

class GetCrossSellProducts extends \Magento\Framework\App\Action\Action {

    /**
     * @var \CustomConcepts\GoCustomizer\Helper\CrossSell
     */
    protected $crossSellHelper;
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $jsonResultFactory;

    /**
     * GetCrossSellProducts constructor.
     * @param Context $context
     * @param \CustomConcepts\GoCustomizer\Helper\CrossSell $crossSellHelper
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory
     */
    public function __construct(
        Context $context,
        \CustomConcepts\GoCustomizer\Helper\CrossSell $crossSellHelper,
        \Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory
    ){
        $this->crossSellHelper = $crossSellHelper;
        $this->jsonResultFactory = $jsonResultFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $result = $this->jsonResultFactory->create();
        $crossSellProducts = $this->crossSellHelper->getCrossSellProducts($this->getRequest()->getParam('sku'));
        $crossSellProducts = $this->crossSellHelper->sortCrosssells($crossSellProducts, true);

        return $result->setData($crossSellProducts);
    }

}
