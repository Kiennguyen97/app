var config = {

    map: {
        '*': {
            loopaddtocart: 'AHT_G1T560/js/loop-addtocart'

        }
    },
    config: {
        mixins: {
            'Magento_Swatches/js/swatch-renderer': {
                'AHT_G1T560/js/swatch-renderer-mixin': true
            },
            'Magento_Swatches/js/catalog-add-to-cart': {
                'AHT_G1T560/js/catalog-add-to-cart-mixin': true
            }
        }
    }
};