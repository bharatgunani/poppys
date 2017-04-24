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



namespace Mirasvit\Giftr\Model\Authorization;

use Magento\Authorization\Model\UserContextInterface;


class GiftRegistrantSessionUserContext implements UserContextInterface
{
    const USER_TYPE_GIFT_REGISTRANT = 5;

    /**
     * @var \Mirasvit\Giftr\Helper\Data
     */
    private $helper;

    /**
     * GiftRegistrantSessionUserContext constructor.
     *
     * @param \Mirasvit\Giftr\Helper\Data $helper
     */
    public function __construct(
        \Mirasvit\Giftr\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        $userId = 0;
        if ($this->helper->isGiftrPurchase()) {
            $userId = $this->helper->getRegistry()->getCustomerId();
        }

        return $userId;
    }

    /**
     * @return int
     */
    public function getUserType()
    {
        return self::USER_TYPE_GIFT_REGISTRANT;
    }
}