<?php

namespace Azguards\UpdateProducts\Setup;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Eav\Model\Config;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    private $eavSetupFactory;
    private $eavConfig;
    private $categorySetupFactory;

    public function __construct(
        EavSetupFactory      $eavSetupFactory,
        Config               $eavConfig,
        CategorySetupFactory $categorySetupFactory
    )
    {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->eavConfig = $eavConfig;
        $this->categorySetupFactory = $categorySetupFactory;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        $eavSetup->addAttribute(
            Product::ENTITY,
            'custom_product_attribute',
            [
                'type' => 'int',
                'label' => 'Custom Product Type',
                'input' => 'select', // Change input type to select
                'source' => '',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'default' => 'Standard', // Set default value to 'Standard'
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => true,
                'used_in_product_listing' => true,
                'option' => [
                    'values' => [
                        'Custom',
                        'Standard'
                    ],
                ],
            ]
        );

        $this->eavConfig->clear();
        $attributeSetId = $this->categorySetupFactory->create()->getDefaultAttributeSetId(Product::ENTITY);
        $attributeGroupId = $this->categorySetupFactory->create()->getDefaultAttributeGroupId(Product::ENTITY, $attributeSetId);
        $attribute = $this->eavConfig->getAttribute(Product::ENTITY, 'custom_product_attribute');
        $attribute->setAttributeSetId($attributeSetId);
        $attribute->setAttributeGroupId($attributeGroupId);
        $attribute->setSortOrder(100);
        $attribute->save();

        $setup->endSetup();
    }
}
