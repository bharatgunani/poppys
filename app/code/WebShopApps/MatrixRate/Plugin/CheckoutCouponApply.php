<?php

namespace WebShopApps\MatrixRate\Plugin;

class CheckoutCouponApply {

    public function beforeSet(\Magento\Quote\Model\CouponManagement $subject, $cartId, $couponCode) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $logger = $objectManager->create('\Psr\Log\LoggerInterface');
        $logger->debug('test before set');
    }

    public function afterSet(\Magento\Quote\Model\CouponManagement $subject, $cartId, $couponCode) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $logger = $objectManager->create('\Psr\Log\LoggerInterface');
        $logger->debug('test after set');
    }

}
