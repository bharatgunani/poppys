<?php

namespace Webindiainc\OrderDetails\Block;

class Success extends \Magento\Checkout\Block\Onepage\Success {

    /*public function getOrder() {
        return $this->_checkoutSession->getLastRealOrder();
    }*/

    public function getSomething() {
        return 'returned something from custom block.';
    }

}
