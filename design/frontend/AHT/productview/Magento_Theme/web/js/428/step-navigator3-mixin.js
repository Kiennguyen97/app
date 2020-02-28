/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * @api
 */
define([
    'jquery',
    'mage/utils/wrapper'
], function ($, wrapper) {
    'use strict';

    var mixin = {
        registerStep: function (code, alias, title, isVisible, navigate, sortOrder) {
            if (code === 'shipping') {
                title = 'Shipping Details';
            }
            if (code === 'payment') {
                title = 'Payment Details';
            }
            this._super();

            console.log('step-mixin');
        }
        
    };

    return function (target) {
        return target.extend(mixin);
    }
});
