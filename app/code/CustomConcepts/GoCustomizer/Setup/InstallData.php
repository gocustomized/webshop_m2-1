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
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface {

    private $eavSetupFactory;

    /**
     * Init
     *
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory) {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context) {

        $connection = $setup->getConnection();
        $setup->startSetup();
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'gocustomizer_product_id', [
            'type' => 'varchar',
            'backend' => '',
            'frontend' => '',
            'label' => 'Customizer product id',
            'input' => 'text',
            'class' => '',
            'source' => '',
            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
            'visible' => true,
            'required' => true,
            'user_defined' => true,
            'is_used_in_grid' => true,
            'default' => '',
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' => false,
            'used_in_product_listing' => true,
            'unique' => false,
            'apply_to' => '',
            'note' => 'Fill in the product id from the customizer product which it should link to.',
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
