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



namespace Mirasvit\Giftr\Block\Item;

class Add extends \Magento\Framework\View\Element\Template
{
    const MODE_CONFIGURE    = 'configure';
    const MODE_ADD          = 'add';

    /**
     * @var \Mirasvit\Giftr\Model\ResourceModel\Registry\CollectionFactory
     */
    protected $registryCollectionFactory;

    /**
     * @var \Mirasvit\Giftr\Model\Config
     */
    protected $config;

    /**
     * @var \Magento\Customer\Model\Url
     */
    protected $customerUrl;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var \Magento\Customer\Model\Session|null
     */
    protected $customerSession = null;

    /**
     * @var \Magento\Framework\View\Element\Template\Context
     */
    protected $context;

    /**
     * @param \Mirasvit\Giftr\Model\ResourceModel\Registry\CollectionFactory $registryCollectionFactory
     * @param \Mirasvit\Giftr\Model\Config                                   $config
     * @param \Magento\Customer\Model\Url                                    $customerUrl
     * @param \Magento\Framework\Registry                                    $registry
     * @param \Magento\Customer\Model\CustomerFactory                        $customerFactory
     * @param \Magento\Framework\View\Element\Template\Context               $context
     * @param array                                                          $data
     */
    public function __construct(
        \Mirasvit\Giftr\Model\ResourceModel\Registry\CollectionFactory $registryCollectionFactory,
        \Mirasvit\Giftr\Model\Config $config,
        \Magento\Customer\Model\Url $customerUrl,
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->registryCollectionFactory = $registryCollectionFactory;
        $this->config = $config;
        $this->customerUrl = $customerUrl;
        $this->registry = $registry;
        $this->customerFactory = $customerFactory;
        $this->context = $context;
        parent::__construct($context, $data);
    }

    /**
     * @var \Mirasvit\Giftr\Model\ResourceModel\Registry\Collection
     */
    private $collection = null;

    /**
     * Get registry collection associated with current customer.
     *
     * @return null|\Mirasvit\Giftr\Model\ResourceModel\Registry\Collection
     */
    public function getCustomerRegistries()
    {
        $customer = $this->customerFactory->create()->load($this->getCustomerSession()->getCustomerId());
        if ($customer && $customer->getId()) {
            if (null === $this->collection) {
                $this->collection = $this->registryCollectionFactory->create()
                    ->addFieldToFilter('customer_id', $customer->getId())
                    ->addIsActiveFilter();
            }
        }

        return $this->collection;
    }

    /**
     * Get registry collection size.
     *
     * @return int
     */
    public function getCountRegistries()
    {
        return $this->getCustomerRegistries() ? $this->getCustomerRegistries()->getSize() : 0;
    }

    /**
     * Return customer session
     *
     * @return \Magento\Customer\Model\Session
     */
    public function getCustomerSession()
    {
        if ($this->customerSession === null) {
            $om = \Magento\Framework\App\ObjectManager::getInstance();
            $this->customerSession = $om->create('Magento\Customer\Model\Session');
        }

        return $this->customerSession;
    }

    /**
     * Is customer logged in.
     *
     * @return bool
     */
    public function isLoggedIn()
    {
        return (bool) $this->getCustomerSession()->isLoggedIn();
    }

    /**
     * Get current product.
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return $this->registry->registry('current_product');
    }

    /**
     * Get add item to registry URL.
     *
     * @return string
     */
    public function getAddUrl()
    {
        return $this->getUrl('giftr/item/add');
    }

    /**
     * Get create registry URL.
     *
     * @return string
     */
    public function getNewRegistryUrl()
    {
        return $this->getUrl('giftr/registry/new');
    }

    /**
     * @return string
     */
    public function getLoginUrl()
    {
        return $this->customerUrl->getLoginUrl();
    }

    /**
     * @return \Mirasvit\Giftr\Model\Item
     */
    public function getItem()
    {
        return $this->registry->registry('registry_item');
    }

    /**
     * @return string
     */
    public function getUpdateUrl()
    {
        return $this->getUrl('*/*/update', ['id' => $this->getItem()->getId()]);
    }

    /**
     * Is validate product selections before adding product to registry.
     *
     * @return int
     */
    public function isValidationNotRequired()
    {
        return (int) $this->config->getIsValidationNotRequired();
    }

    /**
     * Is show registry list for the button "Add to Gift Registry"
     * We do not show registry list when editing existing item.
     *
     * @return int
     */
    public function isShowRegistryList()
    {
        $result = 1;
        if ($this->getRequest()->getModuleName() == 'giftr') {
            $result = (int) ($this->getRequest()->getActionName() != 'configure');
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getJsConfiguration()
    {
        return [
            'Magento_Ui/js/core/app' => [
                'components' => [
                    'giftr-addto__form' => [
                        'component' => 'Mirasvit_Giftr/js/item',
                        'config' => [
                            'url' => $this->getAddUrl(),
                            'registries' => ($this->getCountRegistries())
                                ? $this->getCustomerRegistries()->toOptionArray()
                                : [],
                            'selected' => ($this->getCountRegistries() === 1)
                                ? $this->getCustomerRegistries()->getAllIds()
                                : []
                        ],
                    ],
                ],
            ],
        ];
    }
}
