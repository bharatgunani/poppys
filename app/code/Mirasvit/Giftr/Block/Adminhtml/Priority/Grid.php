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



namespace Mirasvit\Giftr\Block\Adminhtml\Priority;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Mirasvit\Giftr\Model\PriorityFactory
     */
    protected $priorityFactory;

    /**
     * @var \Magento\Backend\Block\Widget\Context
     */
    protected $context;

    /**
     * @var \Magento\Backend\Helper\Data
     */
    protected $backendHelper;

    /**
     * @param \Mirasvit\Giftr\Model\PriorityFactory $priorityFactory
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Backend\Helper\Data          $backendHelper
     * @param array                                 $data
     */
    public function __construct(
        \Mirasvit\Giftr\Model\PriorityFactory $priorityFactory,
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        array $data = []
    ) {
        $this->priorityFactory = $priorityFactory;
        $this->context = $context;
        $this->backendHelper = $backendHelper;
        parent::__construct($context, $backendHelper, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('grid');
        $this->setDefaultSort('priority_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = $this->priorityFactory->create()
            ->getCollection();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('priority_id', [
            'header' => __('ID'),
            'align' => 'center',
            'width' => '100px',
            'index' => 'priority_id',
            'filter_index' => 'main_table.priority_id',
        ]);
        $this->addColumn('name', [
            'header' => __('Title'),
//          'align'     => 'right',
//          'width'     => '50px',
            'index' => 'name',
            'frame_callback' => [$this, '_renderCellName'],
            'filter_index' => 'main_table.name',
        ]);
        $this->addColumn('sort_order', [
            'header' => __('Sort Order'),
//          'align'     => 'right',
//          'width'     => '50px',
            'index' => 'sort_order',
            'filter_index' => 'main_table.sort_order',
        ]);

        return parent::_prepareColumns();
    }

    /**
     * @param $renderedValue
     * @param $item
     * @param $column
     * @param $isExport
     *
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function _renderCellName($renderedValue, $item, $column, $isExport)
    {
        return $item->getName();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('priority_id');
        $this->getMassactionBlock()->setFormFieldName('priority_id');
        $this->getMassactionBlock()->addItem('delete', [
            'label' => __('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => __('Are you sure?'),
        ]);

        return $this;
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', ['id' => $row->getId()]);
    }

    /************************/
}
