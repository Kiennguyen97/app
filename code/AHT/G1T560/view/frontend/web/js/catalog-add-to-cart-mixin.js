/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
require([
    'jquery',
    'Magento_Swatches/js/swatch-renderer'
], function ($,render) {
    'use strict';
    return function (swatch) {
            alert('Hello from SwatchExtend1');
    
            
            return swatch;
        };
    
});
