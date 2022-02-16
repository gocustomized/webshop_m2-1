<?php

namespace CustomConcepts\Base\Model\Config\Source;

class Store {

    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $storeSystemStore;

    public function __construct(
    \Magento\Store\Model\System\Store $storeSystemStore
    ) {
        $this->storeSystemStore = $storeSystemStore;
    }

    public function toOptionArray() {
        return $this->storeSystemStore->getStoreValuesForForm(false, true);
    }

}
