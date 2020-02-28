<?php
namespace AHTG1\G1SJ428\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
class UpgradeSchema implements  UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup,
                            ModuleContextInterface $context){
        $setup->startSetup();
        if (version_compare($context->getVersion(), '1.0.1') < 0) {

            // Get module table
            $tableName1 = $setup->getTable('quote');
            $tableName2 = $setup->getTable('sales_order');
            $tableName3 = $setup->getTable('sales_order_grid');


            // Check if the table already exists
            if ($setup->getConnection()->isTableExists($tableName1) == true && $setup->getConnection()->isTableExists($tableName2) == true && $setup->getConnection()->isTableExists($tableName3) == true) {
                // Declare data
                $columns = [
                    'custom_text' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => false,
                        'comment' => 'Custom Text',
                    ],
                ];

                $connection = $setup->getConnection();
                foreach ($columns as $name => $definition) {
                    $connection->addColumn($tableName1, $name, $definition);
                    $connection->addColumn($tableName2, $name, $definition);
                    $connection->addColumn($tableName3, $name, $definition);
                }

            }
        }

        $setup->endSetup();
    }
}