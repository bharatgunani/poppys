<?php namespace Webindiainc\Cartpricerules\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{

    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $installer->getConnection()->addColumn(
            $installer->getTable('salesrule'),
            'freeshipment',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                'length' => 32,
                'nullable' => false,
                'default' => '0',
				 ['0' => 'No', '1' => 'Yes'],
				 'comment' => 'Select compulsory for freeshipping rule'
							 
           
            ]
        );

        $installer->endSetup();
    }
}?>