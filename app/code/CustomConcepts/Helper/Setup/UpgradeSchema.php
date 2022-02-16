<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace CustomConcepts\Base\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface {

    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context) {

        $setup->startSetup();

        $connection = $setup->getConnection();

        $tableName = $setup->getTable('email_template');

        $connection = $setup->getConnection();

        $connection->query("UPDATE `$tableName` SET `template_text` = replace(template_text, '{{skin', '{{view')");

        $setup->endSetup();
    }

}