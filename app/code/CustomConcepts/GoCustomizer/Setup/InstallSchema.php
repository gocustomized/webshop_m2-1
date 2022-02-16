<?php

/**
 * CustomConcepts_GoCustomizer extension
 * @category  CustomConcepts_Extensions
 * @package   CustomConcepts_GoCustomizer
 * @copyright Copyright (c) 2017
 * @author GoCustomized <info@gocustomized.com>
 */

namespace CustomConcepts\GoCustomizer\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface {

    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context) {

        $connection = $setup->getConnection();
        $installer = $setup;
        $setup->startSetup();
        

        $installer->getConnection()->addColumn(
            $installer->getTable('quote_item'),
            'gocustomizer_data',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'GocustomizerData',
            ]
        );
        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order_item'),
            'gocustomizer_data',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'GocustomizerData',
            ]
        );
        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order_item'),
            'hires_final_png',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'HiresFinalPng',
            ]
        );
        
       $setup->endSetup();
    }

}
