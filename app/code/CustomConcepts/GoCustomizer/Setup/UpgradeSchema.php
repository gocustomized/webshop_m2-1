<?php

/**
 * CustomConcepts_GoCustomizer extension
 * @category  CustomConcepts_Extensions
 * @package   CustomConcepts_GoCustomizer
 * @copyright Copyright (c) 2017
 * @author GoCustomized <info@gocustomized.com>
 */

namespace CustomConcepts\GoCustomizer\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeSchema implements UpgradeSchemaInterface {

    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context) {
        $installer = $setup;

        $installer->startSetup();

        if (version_compare($context->getVersion(), '1.0.1', '<')) {

            $connection = $setup->getConnection();
            $installer = $setup;
            $setup->startSetup();

            $connection->addColumn(
                    $installer->getTable('sales_order_item'), 'exported_id', [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => false,
                'comment' => 'Exported Id',
                    ]
            );

            $connection->addColumn(
                    $installer->getTable('sales_order_item'), 'export_data', [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => false,
                'comment' => 'Exported Data - Store responses of API',
                    ]
            );

            $connection->addColumn(
                    $installer->getTable('sales_order_item'), 'supplier_id', [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'nullable' => true,
                'LENGTH' => 50,
                'comment' => 'Supplier Id',
                    ]
            );

            $setup->endSetup();
        }
    }

}
