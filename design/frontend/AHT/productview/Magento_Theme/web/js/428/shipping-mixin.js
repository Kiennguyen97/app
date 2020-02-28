define([
    'jquery',
    'ko',
    'Magento_Checkout/js/model/step-navigator'
], function($, ko, stepNavigator) {
    'use strict';

    var mixin = {
        initialize: function() {
            this._super();
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

    return function(target) {
        return target.extend(mixin);
    }
});