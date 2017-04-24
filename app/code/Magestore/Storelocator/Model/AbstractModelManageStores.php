<?php

/**
 * Magestore.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Storelocator
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

namespace Magestore\Storelocator\Model;

/**
 * @category Magestore
 * @package  Magestore_Storelocator
 * @module   Storelocator
 * @author   Magestore Developer
 */
abstract class AbstractModelManageStores extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Model constructor.
     *
     * @param \Magento\Framework\Model\Context                   $context
     * @param \Magento\Framework\Registry                        $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb      $resourceCollection
     * @param array                                              $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * @param array $storelocatorIds
     *
     * @return $this
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function pickStores(array $storelocatorIds = [])
    {
        $this->_getResource()->pickStores($this, $storelocatorIds);

        return $this;
    }

    /**
     * @return \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getStores()
    {
        return $this->_getResource()->getStores($this);
    }

    /**
     * @return array
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getStorelocatorIds()
    {
        return $this->_getResource()->getStorelocatorIds($this);
    }

    /**
     * {@inheritdoc}
     */
    public function afterSave()
    {
        parent::afterSave();

        if ($this->hasData('in_storelocator_ids')) {
            $this->pickStores($this->getData('in_storelocator_ids'));
        }

        return $this;
    }
}
