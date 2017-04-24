<?php

namespace Webindiainc\Subscribe\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeSchema implements UpgradeSchemaInterface {

    /**
     * Upgrades DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context) {
        $setup->startSetup();

        $newsletter_subscriber = 'newsletter_subscriber';
        $setup->getConnection()
                ->addColumn(
                        $setup->getTable($orderTable), 'subscriber_name', [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'comment' => 'Subscriber Name'
                        ]
        );
        $setup->getConnection()
                ->addColumn(
                        $setup->getTable($orderTable), 'subscriber_date_of_birth', [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'comment' => 'Date Of Birth'
                        ]
        );
        $setup->getConnection()
                ->addColumn(
                        $setup->getTable($orderTable), 'subscriber_country_code', [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'comment' => 'Country Code'
                        ]
        );

        $setup->endSetup();
    }

}
