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

class Delete extends \Mirasvit\Giftr\Controller\Registry
{
    /**
     * @return void
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            $registry = $this->registryFactory->create()->load($id);
            if ($registry) {
                $registry->delete();
                $this->messageManager->addSuccess('Registry Successfully Deleted');
            } else {
                $this->messageManager->addError('Problem loading registry. Please try again');
            }
        } else {
            $this->messageManager->addError('Insufficient Data Provided');
        }
        $this->messageManager->addSuccess(__('Registry Successfully Deleted'));
        $this->_redirect('*/*/');
    }
}
