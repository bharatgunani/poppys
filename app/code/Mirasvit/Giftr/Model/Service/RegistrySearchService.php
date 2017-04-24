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



namespace Mirasvit\Giftr\Model\Service;

use Mirasvit\Giftr\Model\ResourceModel\Registry\CollectionFactory;

/**
 * Class provides search functionality through the Gift Registries
 */
class RegistrySearchService
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * RegistrySearchService constructor.
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(CollectionFactory $collectionFactory)
    {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Search for registries
     * by default returns only active and associated with current website registries
     *
     * @param array $searchParams           - array of search queries ('search_type' => 'search_query')
     * @param array $searchableAttributes   - actual searchable field names (table `mst_giftr_registry`)
     *
     * @return \Mirasvit\Giftr\Model\ResourceModel\Registry\Collection
     */
    public function search(
        $searchParams,
        array $searchableAttributes = ['firstname', 'lastname', 'co_firstname', 'co_lastname']
    ) {
        $collection = $this->collectionFactory->create()
            ->addFieldToFilter('main_table.is_active', 1)
            ->addWebsiteFilter();

        foreach ($searchParams as $key => $value) {
            $value = trim($value);
            if (!$value) {
                continue;
            }

            switch ((string)$key) {
                case 'uid':
                    $collection->addFieldToFilter($key, $value);
                    GOTO END;
                    break;

                default:
                    $conditions = [];
                    $values = explode(' ', trim($value));
                    foreach ($searchableAttributes as $attribute) {
                        foreach ($values as $searchValue) {
                            $conditions[] .= $attribute . ' LIKE ("%' . addslashes($searchValue) . '%")';
                        }
                    }

                    $collection->getSelect()->where(implode(' OR ', $conditions));
            }
        }

        END:

        return $collection;
    }
}