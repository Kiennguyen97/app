define([
    'jquery'
], function ($) {
    'use strict';
    return {

        test: function (listchild,attributeid) {
            // alert('loop1');
            // console.log('loop1');
            // alert($('input[name=number-option]').val());
            // var numberOption = document.getElementsByName('number-option').value;
            // var numberOption = $("input[type=hidden][name=number-option]").val();
            
            // $('input[name=numberoption]').setAttribute('value',3);
            // var productId = $('[name=product]').val();
            // console.log('productId: ' + productId);
            // if (numberOption>1) {
            //     console.log('loop2');

            // }
            var number = $('[name=number_child]').val();
            // console.log(number);
            var i;
            // var string = 'super_attribute['+ attributeid + ']';
            for (i = 1; i <= number; i++) {
                var quantity = $('input[name=qty'+'-'+i+']').val();
                console.log(quantity);
                if (i==number) {
                    $('input[name=qty]').val(quantity-1);
                }else{
                    $('input[name=qty]').val(quantity);
                }

                // console.log(listchild[i-1]);
                //TODO: True
                 $('input[name="super_attribute['+ attributeid + ']"]').val(listchild[i-1]);   


                // var member_name = $('input[name="super_attribute['+ attributeid + ']"]').val();   
                // console.log(member_name);


                // console.log(attributeid);

                //TODO: False
                // $('input[name='+string+']').val(listchild[i-1]);
                // $('input[name=qty]').val(quantity);

                
                $('#product_addtocart_form').submit();
                $('input[name=qty]').val(0);

            }
        },
        display : function () {
            var number = $('[name=number_option]').val();
            if(number > 1){
                $('input[name=qty]').hide();
                // $('#product_addtocart_form').attr('action','http://localhost/magento1/g1t560/product/addtocart');
            }
        },

        check : function () {
            
        }
        

    }

});