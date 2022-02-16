<?php

/**
 * CustomConcepts_GoCustomizer extension
 * @category  CustomConcepts_Extensions
 * @package   CustomConcepts_GoCustomizer
 * @copyright Copyright (c) 2017
 * @author GoCustomized <info@gocustomized.com>
 */

namespace CustomConcepts\CustomAPI\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface {

    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context) {

        $connection = $setup->getConnection();
        $setup->startSetup();        

        $connection->addColumn(
            $setup->getTable('sales_order'),
            'api_order_id',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => true,
                'LENGTH' => 50,
                'comment' => 'API Order Id',
            ]
        );      
        $connection->addColumn(
            $setup->getTable('integration'),
            'customer_id',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'nullable' => true,
                'LENGTH' => 10,
                'comment' => 'Customer Id',
            ]
        );      
        
       $setup->endSetup();
    }

}
