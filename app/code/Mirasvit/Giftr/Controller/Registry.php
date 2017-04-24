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



namespace Mirasvit\Giftr\Controller;

use Magento\Framework\App\Action\Action;

abstract class Registry extends Action
{
    /**
     * @var \Mirasvit\Giftr\Model\TypeFactory
     */
    protected $typeFactory;

    /**
     * @var \Mirasvit\Giftr\Model\RegistryFactory
     */
    protected $registryFactory;

    /**
     * @var \Magento\Customer\Model\AddressFactory
     */
    protected $addressFactory;

    /**
     * @var \Magento\Customer\Model\FormFactory
     */
    protected $formFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    protected $resultFactory;

    /**
     * @param \Magento\Framework\Data\Form\FormKey\Validator             $formKeyValidator
     * @param \Mirasvit\Giftr\Model\TypeFactory                          $typeFactory
     * @param \Mirasvit\Giftr\Model\RegistryFactory                      $registryFactory
     * @param \Magento\Customer\Model\AddressFactory                     $addressFactory
     * @param \Magento\Customer\Model\FormFactory                        $formFactory
     * @param \Magento\Framework\Registry                                $registry
     * @param \Magento\Customer\Model\Session                            $customerSession
     * @param \Magento\Framework\App\Action\Context                      $context
     */
    public function __construct(
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Mirasvit\Giftr\Model\TypeFactory $typeFactory,
        \Mirasvit\Giftr\Model\RegistryFactory $registryFactory,
        \Magento\Customer\Model\AddressFactory $addressFactory,
        \Magento\Customer\Model\FormFactory $formFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\Action\Context $context
    ) {
        $this->_formKeyValidator = $formKeyValidator;
        $this->typeFactory = $typeFactory;
        $this->registryFactory = $registryFactory;
        $this->addressFactory = $addressFactory;
        $this->formFactory = $formFactory;
        $this->registry = $registry;
        $this->customerSession = $customerSession;
        $this->resultFactory = $context->getResultFactory();
        parent::__construct($context);
    }

    /**
     * @return \Magento\Customer\Model\Session
     */
    protected function _getSession()
    {
        return $this->customerSession;
    }

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(\Magento\Framework\App\RequestInterface $request)
    {
        $action = $this->getRequest()->getActionName();
        if ($action != 'external' && $action != 'postexternal' && $action != 'view') {
            if (!$this->customerSession->authenticate()) {
                $this->getActionFlag()->set('', self::FLAG_NO_DISPATCH, true);
            }
        }

        return parent::dispatch($request);
    }

    /**
     * @return bool|\Mirasvit\Giftr\Model\Registry
     */
    protected function _initRegistry()
    {
        if ($data = $this->getRequest()->getParams()) {
            $registry = $this->registryFactory->create();
            if (isset($data['id'])) {
                $registry->load($data['id']);
            } elseif (isset($data['uid'])) {
                $registry->loadByUid($data['uid']);
            }
            if ($registry && $registry->getId()) {
                $this->registry->register('current_registry', $registry);

                return $registry;
            } else {
                $this->messageManager->addError(__('There was a problem initializing the gift registry'));
            }
        } else {
            $this->messageManager->addError(__('Please select a registry'));
        }

        return false;
    }

    /************************/
}
