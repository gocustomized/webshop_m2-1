<?php


namespace CustomConcepts\CustomPrintCloud\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;

/**
 * Class InstallData
 *
 * @package CustomConcepts\CustomPrintCloud\Setup
 */
class InstallData implements InstallDataInterface
{

    private $eavSetupFactory;

    /**
     * Constructor
     *
     * @param \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function install(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Category::ENTITY,
            'cpc_widget',
            [
                'type' => 'int',
                'label' => 'CPC_widget',
                'input' => 'boolean',
                'sort_order' => 333,
                'source' => '',
                'global' => 0,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => null,
                'group' => 'General Information',
                'backend' => ''
            ]
        );
    }
}

