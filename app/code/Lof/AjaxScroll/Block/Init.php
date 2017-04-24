<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lof_AjaxScroll
 *
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\AjaxScroll\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\ObjectManagerInterface;
use Lof\AjaxScroll\Helper\Data as HelperData; 

class Init extends Template
{   

    protected $_coreRegistry = null;
    /**
     * @var Lof\AjaxScroll\Helper\Data
     */
    protected $helperData; 

    public function __construct(
        Context $context,
        HelperData $helperData,
        ObjectManagerInterface $objectManager,
        \Magento\Framework\Registry $registry,
        array $data = []
        ) {
        $this->objectManager   = $objectManager;
        $this->helperData      = $helperData;
        parent::__construct($context, $data);   
        $this->_coreRegistry   = $registry;



    }

    public function getProductListMode()
    {  
        if ($currentMode = $this->getRequest()->getParam('product_list_mode')) {
            switch($currentMode){
                case 'grid':
                $productListMode = 'grid';
                break;
                case 'list':
                $productListMode = 'list';
                break;
                default:
                $productListMode = 'grid';
            }
        }
        else {
            $defaultMode = $this->helperData->getConfig('catalog/frontend/list_mode'); 
            switch($defaultMode){
                case 'grid-list':
                $productListMode = 'grid';
                break;
                case 'list-grid':
                $productListMode = 'list';
                break;
                case 'list':
                $productListMode = 'list';
                break;
                case 'grid':
                $productListMode = 'grid';
                break;
                default:
                $productListMode = 'grid';
            }
        }

        return $productListMode;
    }

    public function getEnableCategories(){
        $controller = $this->_request->getControllerName();
        if ($controller != 'result') {
            $category = $this->_coreRegistry->registry('current_category')->getId();
            $categories = explode(',', $this->helperData->getConfig('lofajaxscroll/instances/categories'));
            if ($categories) {
                foreach ($categories as $catid) {
                    if ($category == $catid) {
                        return true;
                    }
                }
            }
        } else {
            return false;
        }
        return false;  
    } 

    /**
     * @return bool|false
     */
    public function getLoaderImage()
    {

        $url = $this->helperData->getConfig('lofajaxscroll/design/loading_image');
        if(!empty($url)) {
            $url = strpos($url, 'http') === 0 ? $url : $this->getViewFileUrl($url);
        } 
        return empty($url) ? false : $url;
    }
}
