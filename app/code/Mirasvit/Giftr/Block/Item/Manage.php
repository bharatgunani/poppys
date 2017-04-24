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

class Manage extends //\Magento\Framework\View\Element\Template
    \Magento\Catalog\Block\Product\AbstractProduct
{
    /**
     * @var \Mirasvit\Giftr\Model\ResourceModel\Item\CollectionFactory
     */
    protected $itemCollectionFactory;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $pricingHelper;

    /**
     * @var \Magento\Framework\View\Element\Template\Context
     */
    protected $context;

    /**
     * @var \Mirasvit\Giftr\Model\Registry|null
     */
    protected $registry = null;

    /**
     * @var \Mirasvit\Giftr\Model\ResourceModel\Item\Collection
     */
    private $itemCollection = null;

    /**
     * @param \Mirasvit\Giftr\Model\ItemFactory                          $itemFactory
     * @param \Mirasvit\Giftr\Model\ResourceModel\Item\CollectionFactory $itemCollectionFactory
     * @param \Magento\Framework\Data\Form\FormKey                       $formKey
     * @param \Magento\Framework\Pricing\Helper\Data                     $pricingHelper
     * @param \Magento\Catalog\Block\Product\Context                     $context
     * @param array                                                      $data
     */
    public function __construct(
        \Mirasvit\Giftr\Model\ItemFactory $itemFactory,
        \Mirasvit\Giftr\Model\ResourceModel\Item\CollectionFactory $itemCollectionFactory,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
        \Magento\Catalog\Block\Product\Context $context,
        array $data = []
    ) {
        $this->itemFactory = $itemFactory;
        $this->itemCollectionFactory = $itemCollectionFactory;
        $this->formKey = $formKey;
        $this->pricingHelper = $pricingHelper;
        $this->context = $context;
        parent::__construct($context, $data);
    }

    /**
     * @return \Mirasvit\Giftr\Model\Registry
     */
    public function getRegistry()
    {
        if (null === $this->registry) {
            $this->registry = $this->_coreRegistry->registry('current_registry');
        }

        return $this->registry;
    }

    /**
     * @return \Mirasvit\Giftr\Model\ResourceModel\Item\Collection
     */
    public function getItemCollection()
    {
        if (null == $this->itemCollection) {
            $this->itemCollection = $this->itemCollectionFactory->create()
                ->addFieldToFilter('registry.registry_id', $this->getRegistry()->getId())
                ->setSortOrder();
        }

        return $this->itemCollection;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getAddToCartUrl($itemId = null, $additional = [])
    {
        $formKey = $this->formKey->getFormKey();

        return $this->getUrl('giftr/item/addtocart/', ['form_key' => $formKey]);
    }

    /**
     * Get product price depend on type and options.
     *
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return float|string formatted price
     */
    public function getPrice(\Magento\Catalog\Model\Product $product)
    {
        return $this->pricingHelper->currency($product->getFinalPrice(), true, true);
    }

    /**
     * Can show block add-to-cart or not
     * depends on difference between desired items and invoiced
     *
     * @param \Mirasvit\Giftr\Model\Item $item
     * @return bool
     */
    public function canAddToCart(\Mirasvit\Giftr\Model\Item $item)
    {
        return $item->getQty() > $item->getQtyOrdered();
    }

    /**
     * Check whether the item was already bought or not
     *
     * @param \Mirasvit\Giftr\Model\Item $item
     * @return bool
     */
    public function isComplete(\Mirasvit\Giftr\Model\Item $item)
    {
        return $item->getQty() <= $item->getQtyOrdered();
    }

    /**
     * @param int $itemId
     * @return string
     */
    public function getUpdateUrl($itemId)
    {
        return $this->getUrl('giftr/item/configure', ['id' => $itemId]);
    }

    /**
     * @param int $itemId
     * @return \Mirasvit\Giftr\Model\Item
     */
    public function getItemById($itemId)
    {
        return $this->itemFactory->create()->loadWithOptions($itemId);
    }

    /**
     * @return string
     */
    public function getRemoveItemUrl()
    {
        return $this->getUrl('*/*/delete/');
    }

    /**
     * Get url to gift registry share page
     *
     * @return string
     */
    public function getUrlToShareSection()
    {
        return $this->getUrl('giftr/registry/share/', ['id' => $this->getRegistry()->getId()]);
    }

    /**
     * @return array
     */
    public function getJsConfiguration()
    {
        return [
            '.product-item' => [
                'Magento_Ui/js/core/app' => [
                    'components' => [
                        'giftr__form' => [
                            'component' => 'Mirasvit_Giftr/js/giftr',
                            'config' => [
                                'removeItemUrl' => $this->getRemoveItemUrl(),
                                'addToCartUrl' => $this->getAddToCartUrl()
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
