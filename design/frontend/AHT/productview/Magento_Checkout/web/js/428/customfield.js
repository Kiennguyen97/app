define([
    'jquery',
    'ko',
    'uiComponent'], function ($, ko, Component) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'Magento_Checkout/428/customfield'
            },
            initialize: function () {
                this._super();

                return this;
            },
        });
    });