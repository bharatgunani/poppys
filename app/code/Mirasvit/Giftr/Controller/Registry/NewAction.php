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



namespace Mirasvit\Giftr\Controller\Registry;

use Magento\Framework\Controller\ResultFactory;
use Mirasvit\Giftr\Block\Registry\NewAction as NewRegistry;

class NewAction extends \Mirasvit\Giftr\Controller\Registry
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        if (($typeId = $this->getRequest()->getPost('type_id')) && $typeId === NewRegistry::NEW_EVENT_TYPE) {
            $type = $this->typeFactory->create();
            $exists = $type->getCollection()
                ->addFieldToFilter('name', ['like' => '%"' . $this->getRequest()->getParam('type_name') . '"%'])
                ->getFirstItem();
            if (!$exists->getId()) {
                $type->setName($this->getRequest()->getParam('type_name'))
                    ->setIsActive(false)
                    ->save();
            } else {
                $type = $exists;
            }

            $this->getRequest()->setParams(['type_id' => $type->getId()]);
        }

        $this->getRequest()->setParams([
            'step' => ($this->getRequest()->getParam('step'))
                ? $this->getRequest()->getParam('step')
                : NewRegistry::STEP_START,
        ]);

        return $resultPage;
    }
}
