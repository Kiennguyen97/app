/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'ko',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/resource-url-manager',
    'mage/storage',
    'Magento_Checkout/js/model/payment-service',
    'Magento_Checkout/js/model/payment/method-converter',
    'Magento_Checkout/js/model/error-processor',
    'Magento_Checkout/js/model/full-screen-loader',
    'Magento_Checkout/js/action/select-billing-address',
    'Magento_Checkout/js/model/shipping-save-processor/payload-extender'
], function (
    ko,
    quote,
    resourceUrlManager,
    storage,
    paymentService,
    methodConverter,
    errorProcessor,
    fullScreenLoader,
    selectBillingAddressAction,
    payloadExtender
) {
    'use strict';

    return {
        /**
         * @return {jQuery.Deferred}
         */
        saveShippingInformation: function () {
            var payload;
            // var shippingAddress = quote.shippingAddress();
            // if (shippingAddress['extension_attributes'] === undefined) {
            //     shippingAddress['extension_attributes'] = {};
            // }


            // var extension = [];
            // if (extension['delivery_date'] === undefined) {
            //     extension['delivery_date'] = {};
            // }
            // extension['delivery_date'] = jQuery('[name="delivery_date"]').val();

            // you can extract value of extension attribute from any place (in this example I use customAttributes approach)

            // shippingAddress['extension_attributes']['delivery_date'] = jQuery('[name="delivery_date"]').val();

            /* Assign selected address every time buyer selects address*/
            selectBillingAddressAction(quote.shippingAddress());

            payload = {
                addressInformation: {
                    'shipping_address': quote.shippingAddress(),
                    'billing_address': quote.billingAddress(),
                    'shipping_method_code': quote.shippingMethod()['method_code'],
                    'shipping_carrier_code': quote.shippingMethod()['carrier_code'],
                    extension_attributes: {
                        delivery_date: jQuery('[name="delivery_date"]').val(),
                        custom_text: jQuery('[name="custom_text"]').val()
                  }
                }
            };


            console.log(jQuery('[name="delivery_date"]').val());
            console.log(jQuery('[name="custom_text"]').val());


            // payloadExtender(payload);

            fullScreenLoader.startLoader();

            return storage.post(
                resourceUrlManager.getUrlForSetShippingInformation(quote),
                JSON.stringify(payload)
            ).done(
                function (response) {
                    quote.setTotals(response.totals);
                    paymentService.setPaymentMethods(methodConverter(response['payment_methods']));
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
});
