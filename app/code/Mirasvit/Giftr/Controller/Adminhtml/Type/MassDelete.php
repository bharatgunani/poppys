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



namespace Mirasvit\Giftr\Controller\Adminhtml\Type;

class MassDelete extends \Mirasvit\Giftr\Controller\Adminhtml\Type
{
    /**
     * @return void
     */
    public function execute()
    {
        $ids = $this->getRequest()->getParam('type_id');
        if (!is_array($ids)) {
            $this->messageManager->addError(__('Please select Event Type(s)'));
        } else {
            try {
                foreach ($ids as $id) {
                    $type = $this->typeFactory->create()
                        ->setIsMassDelete(true)
                        ->load($id);
                    $type->delete();
                }
                $this->messageManager->addSuccess(__('Total of %d record(s) were successfully deleted', count($ids)));
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
}
