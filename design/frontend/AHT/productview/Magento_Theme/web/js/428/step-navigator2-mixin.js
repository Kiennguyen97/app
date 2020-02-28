/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * @api
 */
define([
    'jquery',
    'Magento_Checkout/js/model/step-navigator'
], function ($,stepNavigator) {
    'use strict';

    var mixin = {
        hi: function () {
            $.each(stepNavigator.steps(), function(index, step) {
                if (step.code === 'shipping') {
                    step.title = 'Shipping Details';
                }
                if (step.code === 'payment') {
                    step.title = 'Payment Details';
                }
            });
			console.log('shipping-mixin');
        }
        
    };

    return function (target) {
        return target.extend(mixin);
    }
});
