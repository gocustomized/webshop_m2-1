<?php

/**
 * CustomConcepts_Tableratezipranges extension
 * @category  CustomConcepts_Extensions
 * @package   CustomConcepts_Tableratezipranges
 * @copyright Copyright (c) 2017
 * @author GoCustomized <info@gocustomized.com>
 */

namespace CustomConcepts\Tableratezipranges\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{

    /**
     * Update table add a column
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        if ($installer->tableExists('shipping_tablerate')) {
            $installer->getConnection()->addColumn(
                $installer->getTable('shipping_tablerate'),
                'dest_zip_to',
                [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => true,
                'length' => 10,
                'default' => null,
                'comment' => 'Field to make zipcode a range.'
                    ]
            );
        }
        $setup->endSetup();
    }
}
