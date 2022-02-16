<?php

/**
 * CustomConcepts_Giftsection extension
 * @category  CustomConcepts_Extensions
 * @package   CustomConcepts_Giftsection
 * @copyright Copyright (c) 2017
 * @author GoCustomized <info@gocustomized.com>
 */

namespace CustomConcepts\Giftsection\Setup;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * Initialize dependency.
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '1.0.1') < 0) {
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'show_in_gift_popup',
                [
                    'type' => 'varchar',
                    'label' => 'Show in gift popup',
                    'input' => 'boolean',
                    'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                    'required' => false,
                    'apply_to' => 'simple',
                    'used_in_product_listing' => true,
                    'note' => 'Check if product must be shown on gift popup.',
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.0.2') < 0) {
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'gift_item_position',
                [
                    'type' => 'int',
                    'label' => 'Gift Item Position',
                    'input' => 'text',
                    'source' => '',
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                    'required' => false,
                    'apply_to' => 'simple',
                    'used_in_product_listing' => true,
                    'note' => 'Position of item in gift popup box.',
                ]
            );

            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'gift_item_device_type',
                [
                    'type' => 'text',
                    'label' => 'Gift Device Type',
                    'input' => 'multiselect',
                    'source' => \CustomConcepts\Giftsection\Model\Config\Source\GiftItemTypeConfig::class,
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                    'required' => false,
                    'apply_to' => 'simple',
                    'used_in_product_listing' => true,
                    'note' => 'Type of gift item.',
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.0.3') < 0) {
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
            $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY,'gift_item_device_type');
        }
    }

}
