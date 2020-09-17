define([
    'Magento_Ui/js/grid/columns/column',
    'Magento_Catalog/js/product/uenc-processor',
    'Magento_Catalog/js/product/list/column-status-validator',
    'Magento_Customer/js/model/customer',
    'Magento_Customer/js/customer-data',
    'jquery'
], function (Element, uencProcessor, columnStatusValidator, customer, customerData, $) {
    'use strict';

    return Element.extend({
        defaults: {
            label: ''
        },

        /**
         * Get request POST data.
         *
         * @param {Object} row
         * @return {String}
         */
        getDataPost: function (row) {
            return uencProcessor(row['extension_attributes']['wishlist_button'].url);
        },
        getDataId: function (row) {
            return row.id;
        },
        notLogin: function () {
            var customer_email = customerData.get('customer')().email;
            if(!customer_email){
                return true;
            }else{
                return false;
            }

        },
        CustomerLogin: function () {
            var customer_email = customerData.get('customer')().email;
            if(customer_email){
                return true;
            }else{
                return false;
            }

        },

        showpopup: function(row,item, event) {
            var eleme_input = event.currentTarget.children[1];
            eleme_input.setAttribute('checked',true);
            eleme_input.setAttribute('class','button-convert-check-login checked');
        },
        addwishlist: function(row,item, event) {
            var row_id = row.id;
            var url_ = 'ajaxwish/index/index';

            $.ajax({
                    url: url_,
                    type: 'POST',
                    data: {
                        productId: row_id
                    },
                    complete: function(){
                        var message = 'Item added to wishlist';
                        var close = 'x';
                        $("a.convert-close").parent().remove();
                        var appen_mess = $('<div class="message-success-login"><div class"mess_content">'+message+'</div><a class="convert-close">'+close+'</a></div>');

                        $(".message-config").append(appen_mess);
                        $(".message-config").show();

                        $("a.convert-close").click(function (e) {
                            e.preventDefault();
                            $(this).parent().remove();
                            $(".message-config").hide();
                        });
                    }
                });
            $("html, body").animate({
                scrollTop: 0
            }, 1000);
            console.log("end");
        },

        /**
         * Check if component must be shown.
         *
         * @return {Boolean}
         */
        isAllowed: function () {
            return columnStatusValidator.isValid(this.source(), 'add_to_wishlist', 'show_buttons');
        },

        /**
         * Get button label.
         *
         * @return {String}
         */
        getLabel: function () {
            return this.label;
        }
    });
});
