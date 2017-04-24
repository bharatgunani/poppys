<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-gift-registry
 * @version   1.0.21
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



require getcwd() . '/testsuite/Magento/Customer/_files/customer.php';
require getcwd() . '/testsuite/Magento/Catalog/_files/product_simple_duplicated.php';

/* @var $installer Magento\Framework\App\ResourceConnection */
$installer = $objectManager->create('Magento\Framework\App\ResourceConnection');

$queries = [
    "DELETE FROM {$installer->getTableName('mst_giftr_registry')}",
    "DELETE FROM {$installer->getTableName('mst_giftr_item')}",
    "DELETE FROM {$installer->getTableName('mst_giftr_item_option')}",
    "ALTER TABLE {$installer->getTableName('mst_giftr_registry')} AUTO_INCREMENT = 1",
    "ALTER TABLE {$installer->getTableName('mst_giftr_item')} AUTO_INCREMENT = 1",
    "ALTER TABLE {$installer->getTableName('mst_giftr_item_option')} AUTO_INCREMENT = 1",
];

foreach ($queries as $query) {
    $installer->getConnection()->query($query);
}

/* @var $registry Mirasvit\Giftr\Model\Registry */
$registry = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Mirasvit\Giftr\Model\Registry');
$registry
    ->setName('Test registry 1')
    ->setIsActive(1)
    ->setIsPublic(1)
    ->setTypeId(1)
    ->setCustomerId($customer->getId())
    ->setEventAt('2016-03-12')
    ->setLocation('Kiev')
    ->setShippingAddressId(1)
    ->setFirstname($customer->getFirstname())
    ->setLastname($customer->getLastname())
    ->setEmail($customer->getEmail());
$registry->isObjectNew(true);
$registry->save();

// Create gift registry item
/* @var $item Mirasvit\Giftr\Model\Item */
$item = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Mirasvit\Giftr\Model\Item');
$buyRequest = new \Magento\Framework\DataObject(
    [
        'registry_id' => $registry->getId(),
        'product' => $product->getId(),
        'qty' => 1
    ]
);

$item->updateItem($buyRequest);