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



namespace Mirasvit\Giftr\Block\Search;

/**
 * @method \Mirasvit\Giftr\Model\ResourceModel\Registry\Collection getCollection()
 */
class Result extends \Mirasvit\Giftr\Block\Registry\View
{
    /**
     * \Mirasvit\Giftr\Model\Service\RegistrySearchService
     */
    private $searchService;

    /**
     * @var null
     */
    private $enteredData = null;

    /**
     * @param \Mirasvit\Giftr\Model\Service\RegistrySearchService               $searchService,
     * @param \Magento\Framework\Registry                                       $registry
     * @param \Magento\Customer\Model\CustomerFactory                           $customerFactory
     * @param \Magento\Customer\Model\Session                                   $customerSession
     * @param \Magento\Framework\View\Element\Template\Context                  $context
     * @param array                                                             $data
     */
    public function __construct(
        \Mirasvit\Giftr\Model\Service\RegistrySearchService $searchService,
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->searchService = $searchService;
        parent::__construct($registry, $customerFactory, $customerSession, $context, $data);
    }

    /**
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $this->prepareSearchResults();

        return parent::_beforeToHtml();
    }

    /**
     * Prepare search result collection.
     *
     * @return $this
     */
    private function prepareSearchResults()
    {
        $collection = array();
        $registryId = trim($this->getRequest()->getParam('registry_id'));
        $name = trim($this->getRequest()->getParam('name'));
        $giftrVisibility = $this->_scopeConfig->getValue(
            \Mirasvit\Giftr\Model\Config::XML_PATH_GIFTR_VISIBILITY,
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
        );

        if ((int) $giftrVisibility === \Mirasvit\Giftr\Model\Registry::VISIBILITY_PUBLIC || $name || $registryId) {
            $collection = $this->searchService->search(['uid' => $registryId, $name]);
            if (!$registryId) {
                $collection->addFieldToFilter('is_public', 1);
            }
        }

        $this->setCollection($collection);
        //$this->getLayout()->getBlock('pager')->setCollection($collection);

        return $this;
    }

    /**
     * @param string $key
     * @return null|string
     */
    public function getEnteredData($key)
    {
        $value = null;

        if (null === $this->enteredData) {
            $this->enteredData = $this->customerSession
                ->getData('search_form', true);
        }

        if (!empty($this->enteredData) && isset($this->enteredData[$key])) {
            $value = $this->enteredData[$key];
        }

        return $value;
    }

    /**
     * @param \Mirasvit\Giftr\Model\Registry $registry
     * @return void
     */
    public function setRegistry(\Mirasvit\Giftr\Model\Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @return string
     */
    public function getRegistryUrl()
    {
        return $this->getRegistry()->getViewUrl();
    }

    /**
     * @return \Mirasvit\Core\Helper\Image
     */
    public function getImageUrl()
    {
        return $this->getRegistry()->getImageUrl(135, 135);
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function addTitleBlock()
    {
        if ($headBlock = $this->getLayout()->getBlock('head')) {
            $headBlock->setTitle(__('Gift Registry Search'));
        }

        return $this;
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function addBreadcrumbBlock()
    {
        if ($breadcrumb = $this->getLayout()->getBlock('breadcrumbs')) {
            $breadcrumb->addCrumb('home', [
                'label' => __('Home'),
                'title' => __('Home Page'),
                'link' => $this->getBaseUrl(),
            ])->addCrumb('giftr_search', [
                'label' => __('Giftr Registry Search'),
                'title' => __('Giftr Registry Search'),
            ]);
        }

        return $this;
    }
}
