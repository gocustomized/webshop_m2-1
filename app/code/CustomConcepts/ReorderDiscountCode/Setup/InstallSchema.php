<?php

/**
* CustomConcepts_ReorderDiscountCode extension
* @category  CustomConcepts_Extensions
* @package   CustomConcepts_ReorderDiscountCode
* @copyright Copyright (c) 2017
* @author CustomConcepts <info@CustomConcepts.com>
*/

namespace CustomConcepts\ReorderDiscountCode\Setup;

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
        /**
         * Add column into Sales Rulee Coupon Table for store specific coupon generaion
         */
        $installer->getConnection()->addColumn(
                $installer->getTable('salesrule_coupon'), 'store_id', [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            'nullable' => false,
            'comment' => 'Store Id',
                ]
        );
        /**
         * Add column to define the generated coupon by the person
         */
        $installer->getConnection()->addColumn(
                $installer->getTable('salesrule'), 'created_by', [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            40,
            'nullable' => true,
            'comment' => 'Created By',
                ]
        );

        $setup->endSetup();
    }

}
