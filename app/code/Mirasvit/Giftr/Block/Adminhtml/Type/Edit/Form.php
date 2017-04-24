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



namespace Mirasvit\Giftr\Block\Adminhtml\Type\Edit;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var \Mirasvit\Giftr\Model\ResourceModel\Section\CollectionFactory
     */
    protected $sectionCollectionFactory;

    /**
     * @param \Mirasvit\Giftr\Model\ResourceModel\Section\CollectionFactory $sectionCollectionFactory
     * @param \Magento\Framework\Data\FormFactory                           $formFactory
     * @param \Magento\Framework\Registry                                   $registry
     * @param \Magento\Backend\Block\Widget\Context                         $context
     * @param array                                                         $data
     */
    public function __construct(
        \Mirasvit\Giftr\Model\ResourceModel\Section\CollectionFactory $sectionCollectionFactory,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Block\Widget\Context $context,
        array $data = []
    ) {
        $this->sectionCollectionFactory = $sectionCollectionFactory;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Init Form properties.
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('giftr_type_form');
        $this->setTitle(__('Type information'));
    }

    protected function _prepareForm()
    {
        $type = $this->_coreRegistry->registry('current_type');

        $form = $this->_formFactory->create()->setData([
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/save', ['id' => $this->getRequest()->getParam('id'), 'store' => (int) $this->getRequest()->getParam('store')]),
            'method' => 'post',
            'enctype' => 'multipart/form-data',
        ]);

        $fieldset = $form->addFieldset('edit_fieldset', ['legend' => __('General Information')]);
        if ($type->getId()) {
            $fieldset->addField('type_id', 'hidden', [
                'name' => 'type_id',
                'value' => $type->getId(),
            ]);
        }
        $fieldset->addField('store_id', 'hidden', [
            'name' => 'store_id',
            'value' => (int) $this->getRequest()->getParam('store'),
        ]);

        $fieldset->addField('name', 'text', [
            'label' => __('Title'),
            'required' => true,
            'name' => 'name',
            'value' => $type->getName(),
            'scope_label' => __('[STORE VIEW]'),
        ]);
        $fieldset->addField('sort_order', 'text', [
            'label' => __('Sort Order'),
            'name' => 'sort_order',
            'value' => $type->getSortOrder(),
        ]);
        $fieldset->addField('section_ids', '\Mirasvit\Giftr\Block\Adminhtml\Data\Form\Element\Multiselect', [
            'label' => __('Form Sections'),
            'name' => 'section_ids[]',
            'value' => $type->getSectionIds(),
            'values' => $this->sectionCollectionFactory->create()->addActiveFilter()->toOptionArray(),
        ]);
        $fieldset->addField('is_active', 'select', [
            'label' => __('Active'),
            'name' => 'is_active',
            'value' => $type->getIsActive(),
            'values' => [0 => __('No'), 1 => __('Yes')],
        ]);

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
    /************************/
}
