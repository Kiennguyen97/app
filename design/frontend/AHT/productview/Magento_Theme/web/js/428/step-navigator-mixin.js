define(function () {
    'use strict';

    return function (target) { // target == Result that 'Magento_Checkout/js/model/step-navigator' returns.
        // modify target
        var registerStep = target.registerStep;
        target.registerStep = function (code, alias, title, isVisible, navigate, sortOrder) {
            if (code == 'shipping') { // before  method
              title = 'Shipping Details';
            }
            if (code == 'payment') { // before  method
                title = 'Payment Details';
              }
              var result = registerStep.apply(this, [code, alias, title, isVisible, navigate, sortOrder]);
              //after method call
              return  result;
        };
        console.log('step-1');
        return target;
    };
});