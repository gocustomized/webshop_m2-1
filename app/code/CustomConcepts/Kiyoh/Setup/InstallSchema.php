<?php

/**
 * CustomConcepts_Kiyoh extension
 * @category  CustomConcepts_Extensions
 * @package   CustomConcepts_Kiyoh
 * @copyright Copyright (c) 2017
 * @author GoCustomized <info@gocustomized.com>
 */

namespace CustomConcepts\Kiyoh\Setup;

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
         * Create table 'kiyoh_reviews'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('kiyoh_reviews')
        )->addColumn(
            'review_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            10,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'review_id'
        )->addColumn(
            'shop_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            10,
            ['unsigned' => true, 'nullable' => false],
            'Shop Id'
        )->addColumn(
            'company',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => true, 'nullable' => true],
            'Company'
        )->addColumn(
            'kiyoh_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            10,
            ['unsigned' => true, 'nullable' => false],
            'Kiyoh Id'
        )->addColumn(
            'score',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            10,
            ['unsigned' => true, 'default' => 0]
        )->addColumn(
            'score_q2',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            10,
            ['unsigned' => true, 'default' => 0]
        )->addColumn(
            'score_q3',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            10,
            ['unsigned' => true, 'default' => 0]
        )->addColumn(
            'score_q4',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            10,
            ['unsigned' => true, 'default' => 0]
        )->addColumn(
            'score_q5',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            10,
            ['unsigned' => true, 'default' => 0]
        )->addColumn(
            'score_q6',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            10,
            ['unsigned' => true, 'default' => 0]
        )->addColumn(
            'score_q7',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            10,
            ['unsigned' => true, 'default' => 0]
        )->addColumn(
            'score_q8',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            10,
            ['unsigned' => true, 'default' => 0]
        )->addColumn(
            'score_q9',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            10,
            ['unsigned' => true, 'default' => 0]
        )->addColumn(
            'score_q10',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            10,
            ['unsigned' => true, 'default' => 0]
        )->addColumn(
            'customer_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => true, 'nullable' => true],
            'Customer Name'
        )->addColumn(
            'customer_email',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => true, 'nullable' => true],
            'Customer Email'
        )->addColumn(
            'customer_place',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => true, 'nullable' => true],
            'Customer Place'
        )->addColumn(
            'recommendation',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            1,
            ['unsigned' => true, 'nullable' => true],
            'Recommendation'
        )->addColumn(
            'positive',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'Positive'
        )->addColumn(
            'negative',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'Negative'
        )->addColumn(
            'purchase',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'Purchase'
        )->addColumn(
            'reaction',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'Reaction'
        )->addColumn(
            'date_created',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
            'Created date'
        )->addColumn(
            'date_updated',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
            'Last updated date'
        )->addColumn(
            'sidebar',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => 1],
            'Sidebar'
        )->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => 1],
            'Sidebar'
        )->setComment(
            'Kiyoh Reviews'
        );
        $installer->getConnection()->createTable($table);
        /**
         * Create table 'kiyoh_log'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('kiyoh_log')
        )->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            10,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'id'
        )->addColumn(
            'type',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => true, 'nullable' => true],
            'Type'
        )->addColumn(
            'shop_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => true, 'nullable' => false],
            'Shop Id'
        )->addColumn(
            'company',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Company'
        )->addColumn(
            'review_update',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            10,
            ['unsigned' => true, 'default' => 0]
        )->addColumn(
            'review_new',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            10,
            ['unsigned' => true, 'default' => 0]
        )->addColumn(
            'response',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            ['unsigned' => true, 'nullable' => true],
            'Response'
        )->addColumn(
            'order_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            10,
            ['unsigned' => true, 'nullable' => true],
            'Order Id'
        )->addColumn(
            'cron',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Cron'
        )->addColumn(
            'date',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
            'date'
        )->addColumn(
            'time',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Last updated date'
        )->addColumn(
            'api_url',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'Api URL'

        )->setComment(
            'Kiyoh Reviews Log'
        );
        $installer->getConnection()->createTable($table);
        /**
         * 
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('kiyoh_stats')
        )->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            10,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'id'
        )->addColumn(
            'company',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => true, 'nullable' => true],
            'Company'
        )->addColumn(
            'shop_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            10,
            ['unsigned' => true, 'nullable' => false],
            'Shop Id'
        )->addColumn(
            'score',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            10,
            ['unsigned' => true, 'default' => 0]
        )->addColumn(
            'score_q2',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            10,
            ['unsigned' => true, 'default' => 0]
        )->addColumn(
            'score_q2_title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false]
        )->addColumn(
            'score_q3',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            10,
            ['unsigned' => true, 'default' => 0]
        )->addColumn(
            'score_q3_title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false]
        )->addColumn(
            'score_q4',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            10,
            ['unsigned' => true, 'default' => 0]
        )->addColumn(
            'score_q4_title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false]
        )->addColumn(
            'score_q5',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            10,
            ['unsigned' => true, 'default' => 0]
        )->addColumn(
            'score_q5_title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false]
        )->addColumn(
            'score_q6',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            10,
            ['unsigned' => true, 'default' => 0]
        )->addColumn(
            'score_q6_title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false]
        )->addColumn(
            'score_q7',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            10,
            ['unsigned' => true, 'default' => 0]
        )->addColumn(
            'score_q7_title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false]
        )->addColumn(
            'score_q8',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            10,
            ['unsigned' => true, 'default' => 0]
        )->addColumn(
            'score_q8_title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false]
        )->addColumn(
            'score_q9',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            10,
            ['unsigned' => true, 'default' => 0]
        )->addColumn(
            'score_q9_title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false]
        )->addColumn(
            'score_q10',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            10,
            ['unsigned' => true, 'default' => 0]
        )->addColumn(
            'score_q10_title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false]
        )->addColumn(
            'scoremax',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            6,
            ['unsigned' => true, 'default' => 0]        
        )->addColumn(
            'votes',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            5,
            ['nullable' => false, 'default' => 0]
        )->setComment(
            'Kiyoh Stats'
        );
        $installer->getConnection()->createTable($table);
        
       $setup->endSetup();
    }

}
