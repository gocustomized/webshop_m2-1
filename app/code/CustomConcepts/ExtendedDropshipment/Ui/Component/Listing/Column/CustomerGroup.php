<?php

/**
 * CustomConcepts_ExtendedDropshipment extension
 * @category  CustomConcepts_Extensions
 * @package   CustomConcepts_ExtendedDropshipment
 * @copyright Copyright (c) 2017
 * @author GoCustomized <info@gocustomized.com>
 */

namespace CustomConcepts\ExtendedDropshipment\Ui\Component\Listing\Column;

class CustomerGroup extends \Magento\Ui\Component\Listing\Columns\Column {

    protected $groupRepository;

    /**
     * constructor
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
     * @param \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
    \Magento\Customer\Api\GroupRepositoryInterface $groupRepository, \Magento\Framework\View\Element\UiComponent\ContextInterface $context, \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory, array $components = array(), array $data = array()) {
        $this->groupRepository = $groupRepository;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Get customer group by group ID
     * @param type $groupId
     * @return type
     */
    public function getGroupName($groupId) {
        $group = $this->groupRepository->getById($groupId);
        return $group->getCode();
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource) {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['item_id'])) {
                    $item['customer_group_id'] = $this->getGroupName($item['customer_group_id']);
                }
            }
        }
        return $dataSource;
    }

}
