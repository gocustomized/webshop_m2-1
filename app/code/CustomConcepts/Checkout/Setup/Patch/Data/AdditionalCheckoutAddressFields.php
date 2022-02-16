<?php
namespace CustomConcepts\Checkout\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Entity\Attribute\Set as AttributeSet;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;

class AdditionalCheckoutAddressFields implements DataPatchInterface, PatchRevertableInterface
{
    private $moduleDataSetup;

    protected $customerSetupFactory;

    protected $attributeSetFactory;

    public function __construct(
        \Magento\Framework\Setup\ModuleDataSetupInterface $moduleDataSetup,
        CustomerSetupFactory $customerSetupFactory,
        AttributeSetFactory $attributeSetFactory
    ){
        $this->moduleDataSetup = $moduleDataSetup;
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;

    }

    public function apply()
    {
//        /** @var \Magento\Customer\Setup\CustomerSetup $customerSetup */
//        $customerSetup = $this->customerSetupFactory->create(['setup' => $this->moduleDataSetup]);
//        $customerAddressEntity = $customerSetup->getEavConfig()->getEntityType('customer_address');
//        $attributeSetId = $customerAddressEntity->getDefaultAttributeSetId();
//
//        /** @var $attributeSet AttributeSet */
//        $attributeSet = $this->attributeSetFactory->create();
//        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);
//
//        $customerSetup->addAttribute('customer_address', 'housenumber', [
//            'type'          => 'text',
//            'label'         => 'House Number',
//            'input'         => 'text',
//            'required'      =>  false,
//            'visible'       =>  true,
//            'user_defined'  =>  true,
//            'sort_order'    =>  100,
//            'position'      =>  100,
//            'system'        =>  0,
//        ]);
//
//        $attribute = $customerSetup->getEavConfig()->getAttribute('customer_address', 'housenumber')
//            ->addData([
//                'attribute_set_id' => $attributeSetId,
//                'attribute_group_id' => $attributeGroupId,
//                'used_in_forms' => ['adminhtml_customer_address', 'customer_address_edit', 'customer_register_address']
//            ]);
//
//        $attribute->save();
//
//        $customerSetup->addAttribute('customer_address', 'housenumber_addition', [
//            'type'          => 'text',
//            'label'         => 'House Number Addition',
//            'input'         => 'text',
//            'required'      =>  false,
//            'visible'       =>  false,
//            'user_defined'  =>  true,
//            'sort_order'    =>  100,
//            'position'      =>  100,
//            'system'        =>  0,
//        ]);
//
//        $attribute = $customerSetup->getEavConfig()->getAttribute('customer_address', 'housenumber_addition')
//            ->addData([
//                'attribute_set_id' => $attributeSetId,
//                'attribute_group_id' => $attributeGroupId,
//                'used_in_forms' => ['adminhtml_customer_address', 'customer_address_edit', 'customer_register_address']
//            ]);
//
//        $attribute->save();
    }

    public function revert()
    {
        // TODO: Implement revert() method.
    }

    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }
}
