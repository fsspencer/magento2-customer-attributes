<?php
namespace Codealist\CustomerAttributes\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Customer\Model\Customer;
/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var CustomerSetupFactory
     */
    private $customerSetupFactory;
    /**
     * @var AttributeSetFactory
     */
    private $attributeSetFactory;
    /**
     * InstallData constructor.
     * @param CustomerSetupFactory $customerSetupFactory
     * @param AttributeSetFactory $attributeSetFactory
     */
    public function __construct(
        CustomerSetupFactory $customerSetupFactory,
        AttributeSetFactory $attributeSetFactory
    )
    {
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
    }
    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        
        /** @var \Magento\Customer\Setup\CustomerSetup $customerSetup */
        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);
        $customerEntity = $customerSetup->getEavConfig()->getEntityType(Customer::ENTITY);
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();
        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

        $attributesToAdd = [];



        /********* BEGIN: Add CHECKBOX attribute ********* */
        $attrCode = 'my_checkbox_attribute';
        $customerSetup->addAttribute(Customer::ENTITY, $attrCode, [
            'type' => 'int',
            'label' => 'My Checkbox Attribute',
            'input' => 'boolean',
            'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
            'position' => 100,
            'required' => false,
            'default' => false,
            'system' => false
        ]);
        $attributesToAdd[] = $attrCode;
        /********* END ********* */




        /********* BEGIN: Add SELECT/DROPDOWN attribute ********* */
        $attrCode = 'my_select_attribute';
        $customerSetup->addAttribute(Customer::ENTITY, $attrCode, [
            'type' => 'varchar',
            'label' => 'My Select Attribute',
            'input' => 'select',
            'source' => \Magento\Eav\Model\Entity\Attribute\Source\Table::class,
            'option' => [
                'values' => [ 'A', 'B', 'C' ]
            ],
            'position' => 101,
            'required' => false,
            'default' => false,
            'system' => false
        ]);
        $attributesToAdd[] = $attrCode;
        /********* END ********* */




        /********* BEGIN: Add TEXT attribute ********* */
        $attrCode = 'my_text_attribute';
        $customerSetup->addAttribute(Customer::ENTITY, $attrCode, [
            'type' => 'varchar',
            'label' => 'My Text Attribute',
            'input' => 'text',
            'position' => 101,
            'required' => false,
            'default' => "",
            'system' => false
        ]);
        $attributesToAdd[] = $attrCode;
        /********* END ********* */




        /********* BEGIN: Add TEXTAREA attribute ********* */
        $attrCode = 'my_textarea_attribute';
        $customerSetup->addAttribute(Customer::ENTITY, $attrCode, [
            'type' => 'text',
            'label' => 'My Textarea Attribute',
            'input' => 'textarea',
            'position' => 101,
            'required' => false,
            'default' => "",
            'system' => false
        ]);
        $attributesToAdd[] = $attrCode;
        /********* END ********* */




        /** Add the attributes to the attribute set and the common forms */
        foreach ($attributesToAdd as $code) {
            $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, $code);
            $attribute->addData([
                'attribute_set_id' => $attributeSetId,
                'attribute_group_id' => $attributeGroupId,
                'used_in_forms' => ['adminhtml_customer']
            ]);
            $attribute->save();
        }


        $setup->startSetup();
    }

}