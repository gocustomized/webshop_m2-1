<?php

/**
 * CustomConcepts_Kiyoh extension
 * @category  CustomConcepts_Extensions
 * @package   CustomConcepts_Kiyoh
 * @copyright Copyright (c) 2017
 * @author GoCustomized <info@gocustomized.com>
 */

namespace CustomConcepts\Kiyoh\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;

/**
 * Class Time
 */
class Time extends Column {

    /**
     * @var \Magento\Sales\Model\Order
     */
    protected $orderRepository;
    /**
     *
     * @var type ContextInterface
     */
    protected $_context;

    /**
     * Constructor
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param CollectionFactory $collectionFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
    ContextInterface $context, UiComponentFactory $uiComponentFactory, array $components = [], array $data = []
    ) {
        $this->_context = $context;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return void
     */
    public function prepareDataSource(array $dataSource) {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $msg = '';
                if ($item[$this->getData('name')]) {
                    $value = $item[$this->getData('name')];
                    if ($value > 0) {
                        $value = number_format($value, 3, '.', '');
                    }
                    $item[$this->getData('name')] = $value;
                }
            }
        }

        return $dataSource;
    }

}
