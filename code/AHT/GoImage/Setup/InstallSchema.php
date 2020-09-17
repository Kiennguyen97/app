<?php

namespace Gssi\GoImage\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $connection = $installer->getConnection();

        $connection->addColumn('cms_page','go_image',['type' =>\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,'comment' => 'GO Image']);
        $installer->endSetup();
    }
}
