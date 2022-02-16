<?php

/**
 * Product:       Xtento_OrderExport
 * ID:            WoI1MYCFWCFPt2M7HvBsGLGyRIO0K7bzTeJrT/nJ/i0=
 * Last Modified: 2020-07-26T18:37:01+00:00
 * File:          app/code/Xtento/OrderExport/Setup/Uninstall.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\OrderExport\Setup;

use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface;
use Magento\Framework\Setup\ModuleContextInterface;


/**
 * Class Uninstall
 * @package Xtento\OrderExport\Setup
 */
class Uninstall implements UninstallInterface
{
    public function __construct()
    {
    }

    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $connection = $setup->getConnection();
        $connection->dropTable($connection->getTableName('xtento_orderexport_log'));
        $connection->dropTable($connection->getTableName('xtento_orderexport_profile_history'));
        $connection->dropTable($connection->getTableName('xtento_orderexport_profile'));
        $connection->dropTable($connection->getTableName('xtento_orderexport_destination'));

        $setup->endSetup();
    }
}
