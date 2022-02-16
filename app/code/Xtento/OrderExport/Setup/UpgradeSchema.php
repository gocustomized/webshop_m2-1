<?php

/**
 * Product:       Xtento_OrderExport
 * ID:            WoI1MYCFWCFPt2M7HvBsGLGyRIO0K7bzTeJrT/nJ/i0=
 * Last Modified: 2020-06-05T17:58:17+00:00
 * File:          app/code/Xtento/OrderExport/Setup/UpgradeSchema.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\OrderExport\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $connection = $setup->getConnection();

        if (version_compare($context->getVersion(), '2.3.7', '<')) {
            $connection->addIndex(
                $setup->getTable('xtento_orderexport_profile_history'),
                $setup->getIdxName('xtento_orderexport_profile_history', ['entity_id']),
                ['entity_id']
            );
        }

        if (version_compare($context->getVersion(), '2.9.0', '<')) {
            $connection->changeColumn(
                $setup->getTable('xtento_orderexport_destination'), 'port', 'port',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'length' => 5,
                    'unsigned' => true,
                    'nullable' => true,
                    'comment' => 'Port'
                ]
            );
        }

        if (version_compare($context->getVersion(), '2.10.4', '<')) {
            // Move cronjobs into separate cron group
            $connection->query(
                "UPDATE " . $setup->getTable('core_config_data') . " 
                    SET path = REPLACE(path, 'crontab/default/jobs/" . \Xtento\OrderExport\Cron\Export::CRON_GROUP . "', 'crontab/" . \Xtento\OrderExport\Cron\Export::CRON_GROUP . "/jobs/" . \Xtento\OrderExport\Cron\Export::CRON_GROUP . "')
                    WHERE path LIKE 'crontab/default/jobs/" . \Xtento\OrderExport\Cron\Export::CRON_GROUP . "%'"
            );
        }

        if (version_compare($context->getVersion(), '2.10.8', '<')) {
            $connection->addColumn(
                $setup->getTable('xtento_orderexport_destination'),
                'email_send_files_separately',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                    'nullable' => false,
                    'default' => false,
                    'length' => 1,
                    'comment' => 'Send each attachment separately'
                ]
            );
        }

        if (version_compare($context->getVersion(), '2.11.2', '<')) {
            $connection->addColumn(
                $setup->getTable('xtento_orderexport_destination'),
                'ftp_ignorepasvaddress',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                    'nullable' => false,
                    'default' => false,
                    'length' => 1,
                    'comment' => 'FTP Ignore PASV Address'
                ]
            );
        }

        if (version_compare($context->getVersion(), '2.13.1', '<')) {
            $connection->addColumn(
                $setup->getTable('xtento_orderexport_destination'),
                'email_bcc',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => false,
                    'default' => false,
                    'length' => 255,
                    'comment' => 'E-Mail BCC'
                ]
            );
        }

        $setup->endSetup();
    }
}
