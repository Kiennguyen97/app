var config = {
    map: {
        '*': {

            owlcarouselslider:        'Magento_Theme/js/owl.carousel'

        }
    },
    config: {
        mixins: {
            // TODO: True
            'Magento_Checkout/js/view/shipping': {
                'Magento_Theme/js/428/shipping-mixin': true
            },
            // TODO: True
            // 'Magento_Checkout/js/model/step-navigator': {
            //     'Magento_Theme/js/428/step-navigator-mixin': true
            // },
            //TODO: False
            // 'Magento_Checkout/js/model/step-navigator': {
            //     'Magento_Theme/js/428/step-navigator2-mixin': true
            // },
            //TODO: False
            // 'Magento_Checkout/js/model/step-navigator': {
            //     'Magento_Theme/js/428/step-navigator3-mixin': true
            // }
            //TODO: True
            'Magento_Checkout/js/view/summary/abstract-total': {
                'Magento_Theme/js/429/abstract-total-mixin': true
            },
            //TODO: True
            'Magento_Checkout/js/view/summary/shipping': {
                'Magento_Theme/js/429/shipping-mixin': true
            },
        }
    }
};