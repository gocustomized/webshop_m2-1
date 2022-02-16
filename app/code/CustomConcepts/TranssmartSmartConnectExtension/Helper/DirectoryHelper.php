<?php
declare(strict_types=1);

namespace CustomConcepts\TranssmartSmartConnectExtension\Helper;

use Magento\Directory\Model\ResourceModel\Region\Collection;
use Magento\Directory\Model\ResourceModel\Region\CollectionFactory;

class DirectoryHelper
{
    /**
     * @var Collection
     */
    private $collectionFactory;

    public function __construct(
        CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @param string $region
     * @return string|null
     */
    public function getRegionCodeByName(string $region)
    {
        return $this->collectionFactory->create()
            ->addRegionNameFilter($region)
            ->getFirstItem()
            ->getCode();
    }
}
