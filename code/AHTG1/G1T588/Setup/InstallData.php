<?php

namespace AHTG1\G1T588\Setup;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Eav\Setup\EavSetup;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
	private $eavSetupFactory;

	public function __construct(EavSetupFactory $eavSetupFactory,
	\Magento\Catalog\Setup\CategorySetupFactory $categorySetupFactory
	)
	{
		$this->eavSetupFactory = $eavSetupFactory;
		$this->categorySetupFactory = $categorySetupFactory;
	}
	public function install(
		ModuleDataSetupInterface $setup,
		ModuleContextInterface $context
	) {
		$eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
		$setup = $this->categorySetupFactory->create(['setup' => $setup]);         
        $setup->addAttribute(
            \Magento\Catalog\Model\Category::ENTITY, 'custom_image', [
                'type' => 'varchar',
                'label' => 'Custom Image',
                'input' => 'image',
                'backend' => 'Magento\Catalog\Model\Category\Attribute\Backend\Image',
                'required' => false,
                'sort_order' => 9,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'General Information',
            ]
        );

		$eavSetup->addAttribute(
			\Magento\Catalog\Model\Category::ENTITY,
			'yesno',
			[
				'type'         => 'int',
				'label'        => 'Yes/No Attribute',
				'input'        => 'select',
				'source'       => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
				'global'       => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
				'visible'      => true,
				'required'     => false,
				'default'      => null,
				'group'        => 'General Information',
			]
		);
		$eavSetup->addAttribute(
			\Magento\Catalog\Model\Category::ENTITY,
			'select',
			[
				'type'         => 'int',
				'label'        => 'Select Attribute',
				'input'        => 'select',
				'source'       => 'Magento\Catalog\Model\Category\Attribute\Source\Page',
				'global'       => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
				'visible'      => true,
				'required'     => false,
				'default'      => null,
				'group'        => 'General Information',
			]
		);
		$eavSetup->addAttribute(
			\Magento\Catalog\Model\Category::ENTITY,
			'text',
			[
				'type'         => 'text',
				'label'        => 'Text Attribute',
				'input'        => 'text',
				'sort_order'   => 103,
				'source'       => '',
				'global'       => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
				'visible'      => true,
				'required'     => false,
				'default'      => null,
				'group'        => 'General Information',
			]
		);
		$eavSetup->addAttribute(
			\Magento\Catalog\Model\Category::ENTITY,
			'textarea',
			[
				'type'         => 'text',
				'label'        => 'Textarea Attribute',
				'input'        => 'textarea',
				'sort_order'   => 104,
				'source'       => '',
				'global'       => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
				'visible'      => true,
				'required'     => false,
				'default'      => null,
				'group'        => 'General Information',
			]
		);
		
	}
}
