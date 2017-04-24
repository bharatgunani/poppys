<?php

namespace Webindiainc\SaveCustomerGroup\Observer\Customer;

use Magento\Framework\Event\ObserverInterface;

class RegisterSuccess implements ObserverInterface {

    const CUSTOMER_GROUP_ID = 2;

    protected $_customerRepositoryInterface;

    public function __construct(
    \Magento\Framework\App\RequestInterface $request, \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface
    ) {
        $this->_request = $request;
        $this->_customerRepositoryInterface = $customerRepositoryInterface;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {
        $group_id = $this->_request->getParam('group_id');
        if ($group_id != -1) {
            $id = $observer->getEvent()->getCustomer()->getId();
            $customer = $this->_customerRepositoryInterface->getById($id);
            $customer->setGroupId($group_id);
            $this->_customerRepositoryInterface->save($customer);
        }
    }

}
