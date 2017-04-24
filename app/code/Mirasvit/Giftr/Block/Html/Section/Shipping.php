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



namespace Mirasvit\Giftr\Block\Html\Section;

class Shipping extends \Mirasvit\Giftr\Block\Html\Section
{
    public function __construct(
        \Magento\Framework\Data\Collection $collection,
        \Magento\Framework\DataObject $dataObject,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->collection = $collection;
        $this->dataObject = $dataObject;
        $this->customerFactory = $customerFactory;
        $this->customerSession = $customerSession;
        parent::__construct($context, $data);
    }

    public function getFieldCollection()
    {
        $values = ['' => '-- Please Select --'];
        $customer = $this->customerFactory->create()->load($this->customerSession->getCustomerId());
        foreach ($customer->getAddressesCollection() as $address) {
            $values[$address->getId()] = $address->format('default');
        }

        $field = $this->dataObject->setData([
            'name'  => 'Shipping Address Select',
            'class' => 'validate-select',
            'value' => $this->getRegistry()->getShippingAddressId(),
            'code'  => 'shipping_address_id',
            'type'  => 'select',
            'values_as_options' => $values,
            'is_visible' => count($values),
            'is_system' => true,
        ]);
        $this->collection->addItem($field);

        return $this->collection;
    }
}
