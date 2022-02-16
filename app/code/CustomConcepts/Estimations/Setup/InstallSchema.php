<?php
/**
 * Copyright © 2015 CustomConcepts. All rights reserved.
 */

namespace CustomConcepts\Estimations\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
	
        $installer = $setup;

        $installer->startSetup();

		/**
         * Create table 'estimations_deliveryrate'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('estimations_deliveryrate')
        )
		->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'estimations_deliveryrate'
        )
		->addColumn(
            'country_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            3,
            ['nullable' => false],
            'country_id'
        )
		->addColumn(
            'carrier_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false],
            'carrier_id'
        )
		->addColumn(
            'leadtime_min',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false],
            'leadtime_min'
        )
		->addColumn(
            'leadtime_max',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false],
            'leadtime_max'
        )
		->addColumn(
            'week_schedule',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'week_schedule'
        )
		/*{{CedAddTableColumn}}}*/
		
		
        ->setComment(
            'CustomConcepts Estimations estimations_deliveryrate'
        );
		
		$installer->getConnection()->createTable($table);
		/*{{CedAddTable}}*/

        $installer->endSetup();

    }
}
