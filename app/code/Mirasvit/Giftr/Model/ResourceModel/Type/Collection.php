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



namespace Mirasvit\Giftr\Model\ResourceModel\Type;

/**
 * @SuppressWarnings(PHPMD)
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var int
     */
    protected $storeId;

    /**
     * @var string
     */
    protected $_idFieldName = 'type_id';//@codingStandardsIgnoreLine

    /**
     * @var \Magento\Framework\Data\Collection\EntityFactoryInterface
     */
    protected $entityFactory;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Magento\Framework\Data\Collection\Db\FetchStrategyInterface
     */
    protected $fetchStrategy;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Data\Collection
     */
    protected $connection;

    /**
     * @var \Magento\Framework\Model\ResourceModel\Db\AbstractDb
     */
    protected $resource;

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface    $entityFactory
     * @param \Psr\Log\LoggerInterface                                     $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface                    $eventManager
     * @param \Magento\Store\Model\StoreManagerInterface                   $storeManager
     * @param \Magento\Framework\Data\Collection                           $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        $this->entityFactory = $entityFactory;
        $this->logger = $logger;
        $this->fetchStrategy = $fetchStrategy;
        $this->eventManager = $eventManager;
        $this->storeManager = $storeManager;
        $this->connection = $connection;
        $this->resource = $resource;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Mirasvit\Giftr\Model\Type', 'Mirasvit\Giftr\Model\ResourceModel\Type');
    }

    /**
     * @param bool|false $emptyOption
     * @return array
     */
    public function toOptionArray($emptyOption = false)
    {
        $arr = [];
        if ($emptyOption) {
            $arr[0] = ['value' => '', 'label' => __('-- Please Select --')];
        }
        foreach ($this as $item) {
            $arr[] = ['value' => $item->getId(), 'label' => $item->getName()];
        }

        return $arr;
    }

    /**
     * @param bool|false $emptyOption
     * @return array
     */
    public function getOptionArray($emptyOption = false)
    {
        $arr = [];
        if ($emptyOption) {
            $arr[0] = __('-- Please Select --');
        }
        foreach ($this as $item) {
            $arr[$item->getId()] = $item->getName();
        }

        return $arr;
    }

    /**
     * @param int $sectionId
     * @return $this
     */
    public function addSectionFilter($sectionId)
    {
        $this->getSelect()
            ->where("EXISTS (SELECT * FROM `{$this->getTable('mst_giftr_type_section')}`
                AS `type_section_table`
                WHERE main_table.type_id = type_section_table.ts_type_id
                AND type_section_table.ts_section_id in (?))", [0, $sectionId]);

        return $this;
    }

    /**
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        $this->storeId = $storeId;

        return $this;
    }

    /**
     * @return void
     */
    public function _afterLoad()
    {
        if ($this->storeId) {
            foreach ($this as $item) {
                $item->setStoreId($this->storeId);
            }
        }
    }

    /**
     * Filter collection by is_active.
     *
     * @return $this
     */
    public function addIsActiveFilter()
    {
        $this->addFieldToFilter('is_active', true);

        return $this;
    }

    /**
     * Filter collection by active and used but disabled types
     *
     * @param int|null $usedId - if int - add type to collection, if null - join all custom disabled types
     * @return $this
     */
    public function addUsedFilter($usedId = null)
    {
        if ($usedId) {
            $this->addFieldToFilter(
                ['is_active', 'type_id'],
                [1, $usedId]
            );
        } else {
            $this->getSelect()
                ->where(
                    "EXISTS (
                        SELECT 1 FROM `{$this->getTable('mst_giftr_registry')}` AS registry_table
                        WHERE registry_table.type_id = main_table.type_id
                        GROUP BY registry_table.type_id
                    ) OR is_active = 1"
                );
        }

        return $this;
    }

     /************************/
}
