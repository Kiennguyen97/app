/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'uiComponent',
    'Magento_Checkout/js/model/quote',
    'Magento_Catalog/js/price-utils',
    'Magento_Checkout/js/model/totals',
    'Magento_Checkout/js/model/step-navigator'
], function (Component, quote, priceUtils, totals, stepNavigator) {
    'use strict';

    return function (abstractTotal) {
        return abstractTotal.extend({
            isFullMode: function() {
                // if (!this.getTotals() || stepNavigator.getActiveItemIndex() === 1) {
                //     return false;
                // }
                if (!this.getTotals()) {
                    return false;
                }
                return true; //add this line to display forcefully summary in shipping step.
            }
        });
    }
});
