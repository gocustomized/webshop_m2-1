<?php
declare(strict_types=1);

namespace CustomConcepts\Tableratezipranges\Block\Adminhtml\Carrier\Tablerate;

use Bluebirdday\TranssmartSmartConnect\Block\Adminhtml\Carrier\Grid as BluebirddayGrid;
use CustomConcepts\Tableratezipranges\Helper\Config;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Helper\Data;
use Magento\OfflineShipping\Model\Carrier\Tablerate;
use Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate\CollectionFactory;

class Grid extends BluebirddayGrid
{
    /**
     * @var Config
     */
    private $helper;

    public function __construct(
        Context $context,
        Data $backendHelper,
        CollectionFactory $collectionFactory,
        Tablerate $tablerate,
        Config $tableRateZipRangesHelper,
        array $data = []
    ) {
        $this->helper = $tableRateZipRangesHelper;
        parent::__construct($context, $backendHelper, $collectionFactory, $tablerate, $data);
    }


    /**
     * @inheritDoc
     */
    protected function _prepareColumns()
    {
        parent::_prepareColumns();
        if ($this->helper->isTableRateZipRangesEnabled()) {
            $this->addColumn(
                'dest_zip_to',
                ['header' => __('Destination Zip/Postal Code'), 'index' => 'dest_zip_to', 'default' => null]
            );
        }

        return $this;
    }
}
