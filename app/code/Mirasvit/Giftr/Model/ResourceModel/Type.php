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



namespace Mirasvit\Giftr\Model\ResourceModel;

class Type extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @var \Mirasvit\Giftr\Model\SectionFactory
     */
    protected $sectionFactory;

    /**
     * @var \Magento\Framework\Model\ResourceModel\Db\Context
     */
    protected $context;

    /**
     * @var null
     */
    protected $resourcePrefix;

    /**
     * @param \Mirasvit\Giftr\Model\SectionFactory              $sectionFactory
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param  null $resourcePrefix
     */
    public function __construct(
        \Mirasvit\Giftr\Model\SectionFactory $sectionFactory,
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        $resourcePrefix = null
    ) {
        $this->sectionFactory = $sectionFactory;
        $this->context = $context;
        $this->resourcePrefix = $resourcePrefix;
        parent::__construct($context, $resourcePrefix);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('mst_giftr_type', 'type_id');
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return \Magento\Framework\Model\AbstractModel
     */
    protected function loadSectionIds(\Magento\Framework\Model\AbstractModel $object)
    {
        $select = $this->getConnection()->select()
            ->from($this->getTable('mst_giftr_type_section'))
            ->where('ts_type_id = ?', $object->getId());
        if ($data = $this->getConnection()->fetchAll($select)) {
            $array = [];
            foreach ($data as $row) {
                $array[] = $row['ts_section_id'];
            }
            $object->setData('section_ids', $array);
        }

        return $object;
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return void
     */
    protected function saveSectionIds(\Magento\Framework\Model\AbstractModel $object)
    {
        $condition = $this->getConnection()->quoteInto('ts_type_id = ?', $object->getId());
        $this->getConnection()->delete($this->getTable('mst_giftr_type_section'), $condition);
        foreach ((array) $object->getData('section_ids') as $id) {
            $objArray = [
                'ts_type_id' => $object->getId(),
                'ts_section_id' => $id,
            ];
            $this->getConnection()->insert($this->getTable('mst_giftr_type_section'), $objArray);
        }
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        if (!$object->getIsMassDelete()) {
            $this->loadSectionIds($object);
        }

        return parent::_afterLoad($object);
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        if (!$object->getId()) {
            $object->setCreatedAt((new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT));
        }
        $object->setUpdatedAt((new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT));
        $this->addRequiredSections($object);

        return parent::_beforeSave($object);
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        if (!$object->getIsMassStatus()) {
            $this->saveSectionIds($object);
        }

        return parent::_afterSave($object);
    }

    /**
     * Always add to field required sections
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return \Magento\Framework\Model\AbstractModel
     */
    private function addRequiredSections(\Magento\Framework\Model\AbstractModel $object)
    {
        $sectionIds = array_merge(
            array_diff(
                (array) $object->getSectionIds(),
                $this->sectionFactory->create()->getRequiredCollection()->getAllIds()
            ),
            $this->sectionFactory->create()->getRequiredCollection()->getAllIds()
        );

        $object->addData(['section_ids' => $sectionIds]);

        return $object;
    }

    /************************/
}
