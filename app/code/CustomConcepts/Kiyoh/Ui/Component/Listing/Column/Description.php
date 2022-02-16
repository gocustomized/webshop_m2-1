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
 * Class Status
 */
class Description extends Column {

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
                    $type = $item['type'];

                    if ($type == 'reviews') {
                        $updates = '';
                        if ($item['review_new'] > 0) {
                            $msg .= __(sprintf('%s new review(s)', $item['review_new']));
                            $updates++;
                        }
                        if ($item['review_update'] > 0) {
                            if ($updates > 0) {
                                $msg .= ', ';
                            }
                            $msg .= __(sprintf('%s review(s) updated', $item['review_update']));
                            $updates++;
                        }
                        if ($updates > 0) {
                            $msg .= ' & ';
                        }
                        $msg .= __('total score updated');
                    }

                    if ($type == 'invitation') {
                        if ($item['order_id']) {
                            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                            $order = $objectManager->create('\Magento\Sales\Model\Order')
                                    ->load($item['order_id']);
                            if ($order->getId()) {
                                $increment_id = $order->getIncrementId();
                                $order_url = $this->_context->getUrl("sales/order/view", array('order_id' => $item['order_id']));
                                $msg = __(sprintf('%s - Repsonse: %s', '<a href="' . $order_url . '">#' . $increment_id . '</a>', $item['response']));
                            } else {
                                $msg = __(sprintf('Repsonse: %s', $item['response']));
                            }
                        }
                    }
                    $item[$this->getData('name')] = ucfirst($msg);
                }
            }
        }

        return $dataSource;
    }

}
