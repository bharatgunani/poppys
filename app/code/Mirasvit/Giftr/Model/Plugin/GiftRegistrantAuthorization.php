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



namespace Mirasvit\Giftr\Model\Plugin;

use Mirasvit\Giftr\Model\Authorization\GiftRegistrantSessionUserContext;
use Magento\Integration\Api\AuthorizationServiceInterface as AuthorizationService;

/**
 * Plugin around \Magento\Framework\Authorization::isAllowed
 *
 * Plugin to allow guest users who purchase gift registry items to access resources with self permission
 */
class GiftRegistrantAuthorization
{
    /**
     * @var GiftRegistrantSessionUserContext
     */
    protected $userContext;

    /**
     * Inject dependencies.
     *
     * @param GiftRegistrantSessionUserContext $userContext
     */
    public function __construct(GiftRegistrantSessionUserContext $userContext)
    {
        $this->userContext = $userContext;
    }

    /**
     * Check if resource for which access is needed has self permissions defined in webapi config.
     *
     * @param \Magento\Framework\Authorization $subject
     * @param \Closure $proceed
     * @param string $resource
     * @param string $privilege
     *
     * @return bool true If resource permission is self, to allow
     * guest(who purchase gift registry item) access without further checks in parent method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundIsAllowed(
        \Magento\Framework\Authorization $subject,
        \Closure $proceed,
        $resource,
        $privilege = null
    ) {
        if ($resource == AuthorizationService::PERMISSION_SELF
            && $this->userContext->getUserId()
            && $this->userContext->getUserType() === GiftRegistrantSessionUserContext::USER_TYPE_GIFT_REGISTRANT
        ) {
            return true;
        } else {
            return $proceed($resource, $privilege);
        }
    }
}