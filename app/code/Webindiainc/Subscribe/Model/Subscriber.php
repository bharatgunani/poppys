<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Webindiainc\Subscribe\Model;

class Subscriber extends \Magento\Newsletter\Model\Subscriber {

    const STATUS_SUBSCRIBED = 1;
    const STATUS_NOT_ACTIVE = 2;
    const STATUS_UNSUBSCRIBED = 3;
    const STATUS_UNCONFIRMED = 4;
    const XML_PATH_CONFIRM_EMAIL_TEMPLATE = 'newsletter/subscription/confirm_email_template';
    const XML_PATH_CONFIRM_EMAIL_IDENTITY = 'newsletter/subscription/confirm_email_identity';
    const XML_PATH_SUCCESS_EMAIL_TEMPLATE = 'newsletter/subscription/success_email_template';
    const XML_PATH_SUCCESS_EMAIL_IDENTITY = 'newsletter/subscription/success_email_identity';
    const XML_PATH_UNSUBSCRIBE_EMAIL_TEMPLATE = 'newsletter/subscription/un_email_template';
    const XML_PATH_UNSUBSCRIBE_EMAIL_IDENTITY = 'newsletter/subscription/un_email_identity';
    const XML_PATH_CONFIRMATION_FLAG = 'newsletter/subscription/confirm';
    const XML_PATH_ALLOW_GUEST_SUBSCRIBE_FLAG = 'newsletter/subscription/allow_guest_subscribe';
    
    /**
     * Initialize resource model
     *
     * @return void
     */



public function setEmail($value)
    {
        return $this->setSubscriberEmail($value);
    }

    public function subscribe($email, $subscriber_name = '', $subscriber_date_of_birth = '', $subscriber_country_code = '') {

        $this->loadByEmail($email);

        if (!$this->getId()) {
            $this->setSubscriberConfirmCode($this->randomSequence());
        }

        $isConfirmNeed = $this->_scopeConfig->getValue(
                        self::XML_PATH_CONFIRMATION_FLAG, \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                ) == 1 ? true : false;
        $isOwnSubscribes = false;

        $isSubscribeOwnEmail = $this->_customerSession->isLoggedIn() && $this->_customerSession->getCustomerDataObject()->getEmail() == $email;

        if (!$this->getId() || $this->getStatus() == self::STATUS_UNSUBSCRIBED || $this->getStatus() == self::STATUS_NOT_ACTIVE
        ) {
            if ($isConfirmNeed === true) {
                // if user subscribes own login email - confirmation is not needed
                $isOwnSubscribes = $isSubscribeOwnEmail;
                if ($isOwnSubscribes == true) {
                    $this->setStatus(self::STATUS_SUBSCRIBED);
                } else {
                    $this->setStatus(self::STATUS_NOT_ACTIVE);
                }
            } else {
                $this->setStatus(self::STATUS_SUBSCRIBED);
            }
            
            $this->setSubscriberEmail($_POST['email']);
            
           
             
        }

        if (!empty($_POST['subscriber_name'])) {
            $this->setSubscriberName($_POST['subscriber_name']); //subscriber_name            
        }
        if(!empty($_POST['subscriber_dateofbirth'])){
            $this->setSubscriberDateofbirth($_POST['subscriber_dateofbirth']); //date of birth
        }
        if(!empty($_POST['subscriber_countrycode'])){
            $this->setSubscriberCountrycode($_POST['subscriber_countrycode']); //country code
        }
        
        if ($isSubscribeOwnEmail) {
            try {
                $customer = $this->customerRepository->getById($this->_customerSession->getCustomerId());
                $this->setStoreId($customer->getStoreId());
                $this->setCustomerId($customer->getId());
            } catch (NoSuchEntityException $e) {
                $this->setStoreId($this->_storeManager->getStore()->getId());
                $this->setCustomerId(0);
            }
        } else {
            $this->setStoreId($this->_storeManager->getStore()->getId());
            $this->setCustomerId(0);
        }

        $this->setStatusChanged(true);

        try {
            $this->save();
            if ($isConfirmNeed === true && $isOwnSubscribes === false
            ) {
                $this->sendConfirmationRequestEmail();
            } else {
                $this->sendConfirmationSuccessEmail();
            }
            return $this->getStatus();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
    
    protected function createCoupon($coupon = '') {
        try {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $discount_rate = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('subscription/active_display/discount_rate');
            $discount_qty = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('subscription/active_display/discount_qty');
            $discount_type = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('subscription/active_display/discount_type');
            
            
            $code = time();
            $coupon['name'] = 'FREE SHIP FREE TAX ' . $code;
            $coupon['start'] = date("Y-m-d");
            $coupon['end'] = $date = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') + 5, date('Y')));
            $coupon['max_redemptions'] = '1';
            $coupon['discount_type'] = $discount_type;
            $coupon['discount_amount'] = $discount_rate;
            $coupon['discount_qty'] = $discount_qty;
            $coupon['flag_is_free_shipping'] = 0;
            $coupon['redemptions'] = '5';
            $coupon['freeshipment'] = '1';
            $coupon['code'] = $code;
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $shoppingCartPriceRule = $objectManager->create('Magento\SalesRule\Model\Rule');
            $shoppingCartPriceRule->setName($coupon['name'])
                    ->setDescription($coupon['name'])
                    ->setFromDate($coupon['start'])
                    ->setToDate($coupon['end'])
                    ->setUsesPerCustomer($coupon['max_redemptions'])
                    ->setUsesPerCoupon($coupon['max_redemptions'])
                    ->setCustomerGroupIds(array('0', '1', '2', '3',))
                    ->setIsActive('1')
                    ->setSimpleAction($coupon['discount_type'])
                    ->setDiscountAmount($coupon['discount_amount'])
                    ->setDiscountQty($coupon['discount_qty'])
                    ->setDiscountStep('0')
                    ->setApplyToShipping($coupon['flag_is_free_shipping'])
                    ->setTimesUsed($coupon['redemptions'])
                    ->setFreeshipment($coupon['freeshipment'])
                    ->setWebsiteIds(array('1',))
                    ->setCouponType('2')
                    ->setCouponCode($coupon['code']);

            $shoppingCartPriceRule->save();
        } catch (Exception $ex) {
            throw new \Exception($e->getMessage());
        }
        return $code;
    }

    public function sendConfirmationRequestEmail() {
        $code = 'NO COUPON CODE';
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $addtoquote_enable = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('subscription/active_display/active');
        if ($addtoquote_enable) {
            $code = $this->createCoupon();
        }

        $emailTemplateVariables = array();
        $emailTemplateVariables['coponcode'] = $code;

        $postObject = new \Magento\Framework\DataObject();
        $postObject->setData($emailTemplateVariables);


        if ($this->getImportMode()) {
            ##### COMMENT BELOW LINE BCZ MAIL NOT SEND IF IT ON ####
            //return $this;
            ########################################################
        }

        if (!$this->_scopeConfig->getValue(
                        self::XML_PATH_CONFIRM_EMAIL_TEMPLATE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                ) || !$this->_scopeConfig->getValue(
                        self::XML_PATH_CONFIRM_EMAIL_IDENTITY, \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                )
        ) {
            return $this;
        }

        $this->inlineTranslation->suspend();

        $this->_transportBuilder->setTemplateIdentifier(
                $this->_scopeConfig->getValue(
                        self::XML_PATH_CONFIRM_EMAIL_TEMPLATE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                )
        )->setTemplateOptions(
                [
                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => $this->_storeManager->getStore()->getId(),
                ]
        )->setTemplateVars(
                ['subscriber' => $this, 'store' => $this->_storeManager->getStore(), 'data' => $postObject]
        )->setFrom(
                $this->_scopeConfig->getValue(
                        self::XML_PATH_CONFIRM_EMAIL_IDENTITY, \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                )
        )->addTo(
                $this->getEmail(), $this->getName()
        );
        $transport = $this->_transportBuilder->getTransport();
        $transport->sendMessage();

        $this->inlineTranslation->resume();

        return $this;
    }

    /**
     * Sends out confirmation success email
     *
     * @return $this
     */
    public function sendConfirmationSuccessEmail() {

        $code = 'NO COUPON CODE';
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $addtoquote_enable = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('subscription/active_display/active');
        if ($addtoquote_enable) {
            $code = $this->createCoupon();
        }

        $emailTemplateVariables = array();
        $emailTemplateVariables['coponcode'] = $code;

        $postObject = new \Magento\Framework\DataObject();
        $postObject->setData($emailTemplateVariables);
        //var_dump($postObject);exit;

        if ($this->getImportMode()) {
            ##### COMMENT BELOW LINE BCZ MAIL NOT SEND IF IT ON ####
            //return $this;
            ########################################################
        }

        if (!$this->_scopeConfig->getValue(
                        self::XML_PATH_SUCCESS_EMAIL_TEMPLATE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                ) || !$this->_scopeConfig->getValue(
                        self::XML_PATH_SUCCESS_EMAIL_IDENTITY, \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                )
        ) {
            return $this;
        }

        $this->inlineTranslation->suspend();

        $this->_transportBuilder->setTemplateIdentifier(
                $this->_scopeConfig->getValue(
                        self::XML_PATH_SUCCESS_EMAIL_TEMPLATE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                )
        )->setTemplateOptions(
                [
                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => $this->_storeManager->getStore()->getId(),
                ]
        )->setTemplateVars(
                ['subscriber' => $this, 'data' => $postObject]
        )->setFrom(
                $this->_scopeConfig->getValue(
                        self::XML_PATH_SUCCESS_EMAIL_IDENTITY, \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                )
        )->addTo(
                $this->getEmail(), $this->getName()
        );
        $transport = $this->_transportBuilder->getTransport();
        $transport->sendMessage();

        $this->inlineTranslation->resume();

        return $this;
    }
    
    
  }
