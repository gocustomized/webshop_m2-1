<?php

/**
 * Product:       Xtento_OrderExport
 * ID:            WoI1MYCFWCFPt2M7HvBsGLGyRIO0K7bzTeJrT/nJ/i0=
 * Last Modified: 2020-12-20T18:34:45+00:00
 * File:          app/code/Xtento/OrderExport/Model/Export/Data/Custom/Order/AmastyGiftCard.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\OrderExport\Model\Export\Data\Custom\Order;

use Xtento\OrderExport\Model\Export;

class AmastyGiftCard extends \Xtento\OrderExport\Model\Export\Data\AbstractData
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * AmastyGiftCard constructor.
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Xtento\XtCore\Helper\Date $dateHelper
     * @param \Xtento\XtCore\Helper\Utils $utilsHelper
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Xtento\XtCore\Helper\Date $dateHelper,
        \Xtento\XtCore\Helper\Utils $utilsHelper,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $dateHelper, $utilsHelper, $resource, $resourceCollection, $data);
        $this->objectManager = $objectManager;
    }

    public function getConfiguration()
    {
        return [
            'name' => 'Amasty Gift Card Export',
            'category' => 'Order',
            'description' => 'Export data stored by the Amasty Gift Card extension',
            'enabled' => true,
            'apply_to' => [Export::ENTITY_ORDER, Export::ENTITY_INVOICE, Export::ENTITY_SHIPMENT, Export::ENTITY_CREDITMEMO],
            'third_party' => true,
            'depends_module' => 'Amasty_GiftCard',
        ];
    }

    public function getExportData($entityType, $collectionItem)
    {
        // Set return array
        $returnArray = [];
        $this->writeArray = &$returnArray['amasty_giftcards']; // Write on "amasty_giftcards" level

        if (!$this->fieldLoadingRequired('amasty_giftcards')) {
            return $returnArray;
        }

        // Fetch fields to export
        $order = $collectionItem->getOrder();

        try {
            $quoteCollection = $this->objectManager->create('\Amasty\GiftCard\Model\ResourceModel\Quote\CollectionFactory')->create()->getGiftCardsWithAccount($order->getQuoteId());
            foreach ($quoteCollection as $quote) {
                $this->writeArray = & $returnArray['amasty_giftcards'][];
                $this->writeValue('code_id', $quote->getCodeId());
                $this->writeValue('code', $quote->getCode());
                $this->writeValue('gift_amount', $quote->getGiftAmount());
                $this->writeValue('base_gift_amount', $quote->getBaseGiftAmount());
            }
        } catch (\Exception $e) {

        }

        // New Amasty GiftCard version
        try {
            $readHandler = $this->objectManager->create('\Amasty\GiftCardAccount\Model\GiftCardExtension\Order\Handlers\ReadHandler');
            $readHandler->loadAttributes($order);

            if (!$order->getExtensionAttributes() || !$order->getExtensionAttributes()->getAmGiftcardOrder()) {
                return $returnArray;
            }
            $gCardOrder = $order->getExtensionAttributes()->getAmGiftcardOrder();

            foreach ($gCardOrder->getGiftCards() as $card) {
                foreach ($card as $key => $value) {
                    $this->writeArray = &$returnArray['amasty_giftcards'][];
                    $this->writeValue($key, $value);
                }
            }
        } catch (\Exception $e) {

        }

        $this->writeArray = &$returnArray;

        // Done
        return $returnArray;
    }
}