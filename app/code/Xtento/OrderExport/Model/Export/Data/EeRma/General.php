<?php

/**
 * Product:       Xtento_OrderExport
 * ID:            WoI1MYCFWCFPt2M7HvBsGLGyRIO0K7bzTeJrT/nJ/i0=
 * Last Modified: 2019-01-22T16:29:19+00:00
 * File:          app/code/Xtento/OrderExport/Model/Export/Data/EeRma/General.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\OrderExport\Model\Export\Data\EeRma;

class General extends \Xtento\OrderExport\Model\Export\Data\AbstractData
{
    public function getConfiguration()
    {
        return [
            'name' => 'EE RMA Information',
            'category' => 'EeRma',
            'description' => 'Export information stored about Magento Commerce / EE RMA.',
            'enabled' => true,
            'apply_to' => [
                \Xtento\OrderExport\Model\Export::ENTITY_EERMA
            ],
            'third_party' => true,
            'depends_module' => 'Magento_Rma'
        ];
    }

    public function getExportData($entityType, $collectionItem)
    {
        // Set return array
        $returnArray = [];

        // Fetch fields to export

        // Done
        return $returnArray;
    }
}