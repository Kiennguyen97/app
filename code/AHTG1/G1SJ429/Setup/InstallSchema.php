<?php

namespace AHTG1\G1SJ429\Setup;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $installer->getConnection()->addColumn(
            $installer->getTable('quote'),
            'order_comment',
            [
                'type' => 'text',
                'nullable' => false,
                'comment' => 'Order Comment',
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order'),
            'order_comment',
            [
                'type' => 'text',
                'nullable' => false,
                'comment' => 'Order Comment',
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order_grid'),
            'order_comment',
            [
                'type' => 'text',
                'nullable' => false,
                'comment' => 'Order Comment',
            ]
        );

        
        $setup->endSetup();
    }
}