<?php

/**
 * CustomConcepts_GoCustomizer extension
 * @category  CustomConcepts_Extensions
 * @package   CustomConcepts_GoCustomizer
 * @copyright Copyright (c) 2017
 * @author GoCustomized <info@gocustomized.com>
 */

namespace CustomConcepts\GoCustomizer\Setup;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeData implements UpgradeDataInterface {

    private $eavSetupFactory;

    /**
     * Init
     *
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory) {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context) {
        $installer = $setup;

        if (version_compare($context->getVersion(), '1.0.0', '<')) {

            $connection = $installer->getConnection();
            $setup->startSetup();
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

            $eavSetup->addAttribute(
                    \Magento\Catalog\Model\Product::ENTITY, 'size', [
                'type' => 'varchar',
                'backend' => '',
                'frontend' => '',
                'label' => 'Size',
                'input' => 'multiselect',
                'class' => '',
                'source' => '',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'default' => '',
                'searchable' => true,
                'filterable' => true,
                'comparable' => true,
                'visible_on_front' => true,
                'used_in_product_listing' => false,
                'unique' => false,
                'apply_to' => '',
                'note' => 'Specify the size of the tshirt.',
                    ]
            );
            $eavSetup->addAttribute(
                    \Magento\Catalog\Model\Product::ENTITY, 'style', [
                'type' => 'varchar',
                'backend' => '',
                'frontend' => '',
                'label' => 'Style',
                'input' => 'select',
                'class' => '',
                'source' => '',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'default' => '',
                'searchable' => true,
                'filterable' => true,
                'comparable' => true,
                'visible_on_front' => true,
                'used_in_product_listing' => false,
                'unique' => false,
                'apply_to' => '',
                'note' => 'Specify the style of the tshirt.',
                    ]
            );
            $eavSetup->addAttribute(
                    \Magento\Catalog\Model\Product::ENTITY, 'customproduct_color', [
                'type' => 'varchar',
                'backend' => '',
                'frontend' => '',
                'label' => 'Customproduct Color',
                'input' => 'select',
                'class' => '',
                'source' => '',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'default' => '',
                'searchable' => true,
                'filterable' => true,
                'comparable' => true,
                'visible_on_front' => true,
                'used_in_product_listing' => true,
                'unique' => false,
                'apply_to' => '',
                'note' => '',
                'apply_to' => 'simple,virtual,configurable'
                    ]
            );
            $eavSetup->addAttribute(
                    \Magento\Catalog\Model\Product::ENTITY, 'suppliers', [
                'type' => 'varchar',
                'backend' => '',
                'frontend' => '',
                'label' => 'Suppliers',
                'input' => 'select',
                'class' => '',
                'source' => '',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'visible' => true,
                'required' => true,
                'user_defined' => true,
                'default' => '',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'is_filterable_in_grid' => true,
                'is_used_in_grid' => true,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'unique' => false,
                'apply_to' => '',
                'note' => '',
                    ]
            );
            $eavSetup->addAttribute(
                    \Magento\Catalog\Model\Product::ENTITY, 'supplier_sku', [
                'type' => 'varchar',
                'backend' => '',
                'frontend' => '',
                'label' => 'Supplier Skus',
                'input' => 'text',
                'class' => '',
                'source' => '',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'default' => '',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'is_used_in_grid' => true,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'unique' => false,
                'apply_to' => '',
                'note' => '',
                    ]
            );
            $eavSetup->addAttribute(
                    \Magento\Catalog\Model\Product::ENTITY, 'supplier_product_name', [
                'type' => 'varchar',
                'backend' => '',
                'frontend' => '',
                'label' => 'Supplier Product Name',
                'input' => 'text',
                'class' => '',
                'source' => '',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'default' => '',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'is_used_in_grid' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'unique' => false,
                'apply_to' => '',
                'note' => '',
                    ]
            );
            $eavSetup->addAttribute(
                    \Magento\Catalog\Model\Product::ENTITY, 'supplier_version_name', [
                'type' => 'varchar',
                'backend' => '',
                'frontend' => '',
                'label' => 'Supplier Version Name',
                'input' => 'text',
                'class' => '',
                'source' => '',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'default' => '',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'is_used_in_grid' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'unique' => false,
                'apply_to' => '',
                'note' => '',
                    ]
            );


            /* $eavSetup->addAttribute(
              'quote_item',
              'gocustomizer_data', array(
              'type' => 'text',
              'nullable' => true,
              'grid' => false,
              )
              );
              $eavSetup->addAttribute(
              'order_item',
              'gocustomizer_data', array(
              'type' => 'text',
              'nullable' => true,
              'grid' => false,
              )
              ); */

            $setup->startSetup();
        }
    }

}
