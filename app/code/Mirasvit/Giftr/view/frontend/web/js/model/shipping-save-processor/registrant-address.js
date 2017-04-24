define(
    [
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/resource-url-manager',
        'mage/storage',
        'Magento_Checkout/js/model/payment-service',
        'Magento_Checkout/js/model/payment/method-converter',
        'Magento_Checkout/js/model/error-processor',
        'Magento_Checkout/js/model/full-screen-loader',
        'uiRegistry'
    ],
    function (
        quote,
        resourceUrlManager,
        storage,
        paymentService,
        methodConverter,
        errorProcessor,
        fullScreenLoader,
        registry
    ) {
        'use strict';

        return {
            saveShippingInformation: function () {
                var payload;

                quote.billingAddress.subscribe(function(giftrAddress) {
                    // After quote billing address is selected - update current billing address in billing address view component
                    var billingAddressView = registry.get({component: "Magento_Checkout/js/view/billing-address"});
                    if (billingAddressView) {
                        billingAddressView.currentBillingAddress(quote.billingAddress());
                    }
                });

                // Gift registrant address should not be saved for billing
                /*if (!quote.billingAddress()) {
                    selectBillingAddressAction(quote.shippingAddress());
                }*/

                payload = {
                    addressInformation: {
                        shipping_address: quote.shippingAddress(),
                        billing_address: quote.billingAddress(),
                        shipping_method_code: quote.shippingMethod().method_code,
                        shipping_carrier_code: quote.shippingMethod().carrier_code
                    }
                };

                fullScreenLoader.startLoader();

                return storage.post(
                    resourceUrlManager.getUrlForSetShippingInformation(quote),
                    JSON.stringify(payload)
                ).done(
                    function (response) {
                        quote.setTotals(response.totals);
                        paymentService.setPaymentMethods(methodConverter(response.payment_methods));
                        fullScreenLoader.stopLoader();
                    }
                ).fail(
                    function (response) {
                        errorProcessor.process(response);
                        fullScreenLoader.stopLoader();
                    }
                );
            }
        };
    }
);
