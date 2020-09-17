require([
    'jquery',
    'ryco/owlcarousel',
    'Magento_Catalog/js/price-utils'
], function (jQuery, owlCarousel, priceUtils) {
    (function ($) {
        $(document).ready(function () {
            addNewArea();
            handleXyInputChange();
            setTax();
            changeYd();

            $('body').on('click', '.search-toggle', function () {
                $(this).toggleClass('active');
                $(this).parents().parent().find('.middle-header-content .search-form').slideToggle();
                $(this).parents().parent().find('#search').focus();
            });

            $('body').on('click', '.accordion-title', function () {
                if($(this).find('i.fal').hasClass('fa-plus')){
                    $(this).find('i.fal').removeClass('fa-plus');
                    $(this).find('i.fal').addClass('fa-minus');
                }else if($(this).find('i.fal').hasClass('fa-minus')){
                    $(this).find('i.fal').removeClass('fa-minus');
                    $(this).find('i.fal').addClass('fa-plus');
                }
            });
            
            $('.ryco_toolbar_show_div button').click(function () {
                $('.catalog-category-view #ryco_toolbar_div').toggleClass('show_ryco_toolbar');
                $('.catalogsearch-result-index #ryco_toolbar_div').toggleClass('show_ryco_toolbar');
            });

            $('.show_filter').click(function () {
                $(this).toggleClass('show_block');
                $('.catalog-category-view .block.filter').toggleClass('show_block');
                $('.catalogsearch-result-index .block.filter').toggleClass('show_block');
            });

            $('body').on('click', '#irwintiles_floor_caculator', function () {
                $(this).parents().parent().find(".product-wrap-inner .product-info-main-wrapper").toggleClass('active');
            });

            $(document).ready(function() {
                $(".options-list .choice:last-child input.product-custom-option").attr('checked', 'checked');
                if($("body").find(".block.filter").length) {
                    $(".show_fiter_div .show_filter").show();
                }
            });

            $('body').on('click', '#irwintiles_samples_request', function () {
                $(".options-list .choice:nth-child(2) input.product-custom-option").prop("checked", true);
                $(".options-list .choice:nth-child(1) input.product-custom-option").attr('checked', 'checked');
                $("#qty").val(1);
                $("#product-addtocart-button").trigger('click');
                $(".options-list .choice:nth-child(1) input.product-custom-option").prop("checked", true);
                $(".options-list .choice:nth-child(2) input.product-custom-option").attr('checked', 'checked');
            });    

            $('body').on('click', '.product-info-price .unit-switcher li', function () {
                var unit = $(this).children('input[name*="unit-switcher"]').val();
                $(this).parents().find(".price-box.price-final_price .unit-price").html(unit);
            });
            $('body').on('click', '.product-info-price .unit-switcher li.unit-m', function () {
                $('#product_unit_switcher').prop('checked', false).trigger("change");
            });
            $('body').on('click', '.product-info-price .unit-switcher li.unit-yards', function () {
                $('#product_unit_switcher').prop('checked', true).trigger("change");
            });
            console.log(calculateTotal());
           // update total price when switch unit
            $('#product_unit_switcher').on('change', function (e) {
                var total = parseFloat(calculateTotal());
                if($('.catalog-product-view #qty-total').attr('data-first-loaded') === 'true'){
                    total = $('.catalog-product-view #qty-total').val();
                    $('.catalog-product-view #qty-total').attr('data-first-loaded','false');
                }
                if($('.catalog-product-view #qty-total').attr('data-unit-metres') === 'true'){
                    total = parseFloat($('.catalog-product-view #qty-total').val())*1.1959;
                    $('.catalog-product-view #qty-total').attr('data-unit-metres','false');
                }
                if (isNaN(total)) {
                    $('#ryco_toolbar_result .ryco_toolbar_output input').val(0);
                } else {
                    $('#ryco_toolbar_result .ryco_toolbar_output input').val(parseFloat(total).toFixed(2));
                }
            });

            $("#narrow-by-list2 .filter-options-title").click(function(){
                $(this).next().slideToggle();
            })
        })

        function setTax() {
            var qty;
            var pricenow;
            var currency = $('.product-info-price .price-box .price-container [data-price-type=finalPrice]>.price').text().replace(/[0-9]|[./]|[a-zA-Z0-9]/g, "");
            $('.set_price_tax .include_tax').click(function () {
                $(this).addClass('active');
                $('.set_price_tax .exclude_tax').removeClass('active');
                $('.catalog-category-view .product-items .price-box').hide();
                $('.catalog-category-view .product-items .price-box.price-include-tax').show();
                $('.catalogsearch-result-index .product-items .price-box').hide();
                $('.catalogsearch-result-index .product-items .price-box.price-include-tax').show();
                total = calculateTotal();
                calculateResult(total, true);
            });

            $('.set_price_tax .exclude_tax').click(function () {
                $(this).addClass('active');
                $('.set_price_tax .include_tax').removeClass('active');
                $('.catalog-category-view .product-items .price-box').show();
                $('.catalog-category-view .product-items .price-box.price-include-tax').hide();
                $('.catalogsearch-result-index .product-items .price-box').show();
                $('.catalogsearch-result-index .product-items .price-box.price-include-tax').hide();
                total = calculateTotal();
                calculateResult(total, false);
            });

            $('.prices-tier-switcher #inc_vat').click(function () {
                $(this).addClass('active');
                $('.prices-tier-switcher #ex_vat').removeClass('active');
                $(".price-container .price-excluding-tax").hide();
                $(".price-container .tax-label.exc-vat").hide();
                $(".price-container .price-including-tax").show();
                $(".price-container .tax-label.inc-vat").show();
                $('.product-item-info .price-box').hide();
                $('.product-item-info .price-box.price-include-tax').show();
                pricenow = $('.product-info-price .price-box .price-container [data-price-type=finalPrice]').attr('data-price-amount');
                qty = $('.box-tocart .field.qty #qty').val();
                $('.pricing .price .value').text(currency + (pricenow * qty).toFixed(2));
            });

            $('.prices-tier-switcher #ex_vat').click(function () {
                $(this).addClass('active');
                $('.prices-tier-switcher #inc_vat').removeClass('active');
                $(".price-container .price-excluding-tax").show();
                $(".price-container .tax-label.exc-vat").show();
                $(".price-container .price-including-tax").hide();
                $(".price-container .tax-label.inc-vat").hide();
                $('.product-item-info .price-box').show();
                $('.product-item-info .price-box.price-include-tax').hide();
                pricenow = $('.product-info-price .price-box .price-container [data-price-type=basePrice]').attr('data-price-amount');
                qty = $('.box-tocart .field.qty #qty').val();
                $('.pricing .price .value').text(currency + (pricenow * qty).toFixed(2));
            });


            /*Dropdown Menu*/
             $(document).ready(function() {
                if($(".list-subcate-left .level1").hasClass("active")){
                    var id_cate_left = $(".list-subcate-left .level1.active").attr("id-cate-attr");
                    var cateId = "#" + id_cate_left;
                    $(cateId).show();
                }
            });

            // $(".mega-menu-item.level0.dropdown").hover(function(){
            //     $(".list-subcate-left .level1.active").trigger("mouseover");
            // })

            $(".list-subcate-left .level1").mouseover(function(){
                var id_cate_left = $(this).attr("id-cate-attr");
                var cateId = "#" + id_cate_left;
                if($(this).hasClass("active")){
                    $(cateId).show();
                }
                $(cateId).show();
                $(cateId).siblings().hide();
            });

            $( ".product-detail-update-layout .caculator-price-wrapper .field.qty" ).insertBefore($(".product-detail-update-layout .caculator-cart-wrapper .actions"));
          //   $("#qty").val(1);
        }

        function changeYd() {
            var currency = $('.product-info-price .price-box .price-container [data-price-type=finalPrice]>.price').text().replace(/[0-9]|[./]|[a-zA-Z0-9]/g, "");
            var cover = $("#ryco_toolbar_calculator").data('cover')?parseFloat($("#ryco_toolbar_calculator").data('cover')):parseFloat($("#ryco_product_calculator").data('cover'));
            if(isNaN(cover)){
                cover = 1;
            }
            var finalPrice = parseFloat($('.product-info-price .price-box .price-container [data-price-type=finalPrice]').data('price-amount')/cover);
            var basePrice = parseFloat($('.product-info-price .price-box .price-container [data-price-type=basePrice]').data('price-amount')/cover);
            var oldPrice = parseFloat($('.product-info-price .price-box .price-container [data-price-type=oldPrice]').data('price-amount')/cover);
            var oldbasePrice = parseFloat($('.product-info-price .price-box .price-container [data-price-type=oldbasePrice]').data('price-amount')/cover);
            $('.product-info-price .unit-switcher .unit-m').on('click', function () {
                if (!$('.product-info-price .unit-switcher .unit-m input[name="unit-switcher"]').is(':checked')) {
                    $('.product-info-price .price-box .price-container [data-price-type=finalPrice]>.price').text(currency + parseFloat(finalPrice * 1.1959).toFixed(2));
                    $('.product-info-price .price-box .price-container [data-price-type=basePrice]>.price').text(currency + parseFloat(basePrice * 1.1959).toFixed(2));
                    $('.product-info-price .price-box .price-container [data-price-type=oldPrice]>.price').text(currency + parseFloat(oldPrice * 1.1959).toFixed(2));
                    $('.product-info-price .price-box .price-container [data-price-type=oldbasePrice]>.price').text(currency + parseFloat(oldbasePrice * 1.1959).toFixed(2));
                }
            });
            $('.product-info-price .unit-switcher .unit-yards').on('click', function () {
                if (!$('.product-info-price .unit-switcher .unit-yards input[name="unit-switcher"]').is(':checked')) {
                    $('.product-info-price .price-box .price-container [data-price-type=finalPrice]>.price').text(currency + finalPrice.toFixed(2));
                    $('.product-info-price .price-box .price-container [data-price-type=basePrice]>.price').text(currency + basePrice.toFixed(2));
                    $('.product-info-price .price-box .price-container [data-price-type=oldPrice]>.price').text(currency + oldPrice.toFixed(2));
                    $('.product-info-price .price-box .price-container [data-price-type=oldbasePrice]>.price').text(currency + oldbasePrice.toFixed(2));
                }
            });
            //TODO: fix switch
            $('.catalog-product-view .unit_switcher .toggle-switch').on('click', function () {
                var isYd = $('#product_unit_switcher').is(':checked');
                var m = $('.unit-m input[name="unit-switcher"]').val();
                var yd = $('.unit-yards input[name="unit-switcher"]').val();

                if (isYd) {
                    $(".product-info-price .price-box.price-final_price .unit-price").html(m);
                    $('.info-price-wrapper .collect-deliver li.unit-m #unit-f-option1').attr('checked', 'checked');
                    $('.product-info-price .price-box .price-container [data-price-type=finalPrice]>.price').text(currency + parseFloat(finalPrice * 1.1959).toFixed(2));
                    $('.product-info-price .price-box .price-container [data-price-type=basePrice]>.price').text(currency + parseFloat(basePrice * 1.1959).toFixed(2));
                    $('.product-info-price .price-box .price-container [data-price-type=oldPrice]>.price').text(currency + parseFloat(oldPrice * 1.1959).toFixed(2));
                    $('.product-info-price .price-box .price-container [data-price-type=oldbasePrice]>.price').text(currency + parseFloat(oldbasePrice * 1.1959).toFixed(2));
                } else {
                    $(".product-info-price .price-box.price-final_price .unit-price").html(yd);
                    $('.info-price-wrapper .collect-deliver li.unit-yards #unit-f-option2').attr('checked', 'checked');
                    $('.product-info-price .price-box .price-container [data-price-type=finalPrice]>.price').text(currency + finalPrice.toFixed(2));
                    $('.product-info-price .price-box .price-container [data-price-type=basePrice]>.price').text(currency + basePrice.toFixed(2));
                    $('.product-info-price .price-box .price-container [data-price-type=oldPrice]>.price').text(currency + oldPrice.toFixed(2));
                    $('.product-info-price .price-box .price-container [data-price-type=oldbasePrice]>.price').text(currency + oldbasePrice.toFixed(2));
                }
            });
        }
        function addNewArea() {
            // handle click "add new area" in price calculator 
            $('#ryco_toolbar_calculator .add_area').click(function () {
                // check the unit is "m" or "yd"
                var isYd = $('#product_unit_switcher').is(':checked');
                var unittext = 'm';
                if (isYd) {
                    //IT-27
                    // unit = 'yd';
                    unittext = 'ft';
                }
                var html = '<div class="xy_div">';
                html += '<input type="number" min="0" name="width" placeholder="Width" class="width">';
                html += '<label class="total-placeholder-1" for="width">' + unittext + '</label>';
                html += '<span class="separator">x</span>';
                html += '<input type="number" min="0" name="length" placeholder="Length" class="length">';
                html += '<label class="total-placeholder-2" for="length">' + unittext + '</label>';
                html += '<div class="xy-remove" title="remove">x</div>';
                html += '</div>';

                $('#ryco_toolbar_calculator .layout_xy').append(html);
                handleXyInputChange();
            });
            $('#ryco_toolbar_calculator .layout_xy').on('click', 'div.xy-remove', function (e) {
                $(this).parent('.xy_div').remove();
                var total = calculateTotal();
                if (isNaN(total)) {
                    //IT-27
                    $('#ryco_product_calculator .ryco-toolbar__layouts input[name="total"]').val(customCalTotal().toFixed(2));
                    $('#ryco_product_calculator .ryco-toolbar__layouts input[name="total"]').trigger("change");
                    $('#ryco_toolbar_result .ryco_toolbar_output input').val(0);
                } else {
                    //IT-27
                    $('#ryco_product_calculator .ryco-toolbar__layouts input[name="total"]').val(customCalTotal().toFixed(2));
                    $('#ryco_product_calculator .ryco-toolbar__layouts input[name="total"]').trigger("change");
                    total = parseFloat(calculateTotal());
                    $('#ryco_toolbar_result .ryco_toolbar_output input').val(total.toFixed(2));

                }
            });
        }

         function handleXyInputChange() {
            // update result in area when change input value in price calculator
            $('#ryco_toolbar_calculator .layouts input').on('keyup change', function(){
                var total = parseFloat(calculateTotal());
                if (isNaN(total)) {
                    $('#ryco_product_calculator .ryco-toolbar__layouts input[name="total"]').val(customCalTotal().toFixed(2));
                    $('#ryco_product_calculator .ryco-toolbar__layouts input[name="total"]').trigger("change");
                    $('#ryco_toolbar_result .ryco_toolbar_output input').val(0);
                } else {
                    // $('#ryco_toolbar_result .ryco_toolbar_output input').val(total.toFixed(2));
                    $('#ryco_product_calculator .ryco-toolbar__layouts input[name="total"]').val(customCalTotal().toFixed(2));
                    $('#ryco_product_calculator .ryco-toolbar__layouts input[name="total"]').trigger("change");
                    total = parseFloat(calculateTotal());
                    $('#ryco_toolbar_result .ryco_toolbar_output input').val(total.toFixed(2));
                }
            });
        }
        // customFunction calculate hidden input in product page
        function customCalTotal(){
            if ($('#ryco_toolbar_calculator .layout_switcher label.m').hasClass('active')) {
                total = parseFloat($('#ryco_toolbar_calculator .inputs input[name="total"]').val());
            } else {
                var n = 1;
                var widthArr = {};
                // specify layout_xy element to calculate total (change from 'inputs')
                $('#ryco_toolbar_calculator .layout_xy input[name="width"]').each(function (i, obj) {
                    widthArr[n] = parseFloat($(obj).val());
                    n++;
                });
                var n = 1;
                var lengthArr = {};
                $('#ryco_toolbar_calculator .layout_xy input[name="length"]').each(function (i, obj) {
                    lengthArr[n] = parseFloat($(obj).val());
                    n++;
                });
                var total = parseFloat(0);
                for (var i = 1; i < n; i++) {
                    total += parseFloat(widthArr[i]) * parseFloat(lengthArr[i]);
                }
                if($('.xy_div_floor_measurements').length){
                    var floorWidth = parseFloat($('.xy_div_floor_measurements input[name="width"]').val());
                    var floorLength = parseFloat($('.xy_div_floor_measurements input[name="length"]').val());
                    if(!isNaN(floorWidth)&&!isNaN(floorLength)) total += floorLength*floorWidth;
                }
            }
            // var isYd = $('#product_unit_switcher').is(':checked');
            
            // if (isYd){
            //     total = total * (1/9) ;
            // }
            // if(total == null || total == ''){
            //     total = 0;  
            // }
            return total;
        }
        function calculateTotal() {
            // if xy is checked, then calculate total by this formula:
            // (length * width) + (length * width)
            // if m is checked instead of xy, then will use input[name="total"] as total
            if ($('#ryco_toolbar_calculator .layout_switcher label.m').hasClass('active')) {
                total = $('#ryco_toolbar_calculator .inputs input[name="total"]').val();
            } else {
                var n = 1;
                var widthArr = {};
                // specify layout_xy element to calculate total (change from 'inputs')
                $('#ryco_toolbar_calculator .layout_xy input[name="width"]').each(function (i, obj) {
                    widthArr[n] = parseFloat($(obj).val());
                    n++;
                });
                var n = 1;
                var lengthArr = {};
                $('#ryco_toolbar_calculator .layout_xy input[name="length"]').each(function (i, obj) {
                    lengthArr[n] = parseFloat($(obj).val());
                    n++;
                });
                var total = parseInt(0);
                for (var i = 1; i < n; i++) {
                    total += parseFloat(widthArr[i]) * parseFloat(lengthArr[i]);
                }
                if($('.xy_div_floor_measurements').length){
                    var floorWidth = parseFloat($('.xy_div_floor_measurements input[name="width"]').val());
                    var floorLength = parseFloat($('.xy_div_floor_measurements input[name="length"]').val());
                    if(!isNaN(floorWidth)&&!isNaN(floorLength)) total += floorLength*floorWidth;
                }
            }
            var isYd = $('#product_unit_switcher').is(':checked');
            // if (!isYd){
            //     total = total * 1.1959;
            // }
            // IT-27
            if (!isYd){
                total = total * 1.1959;
            }else {
                total = total * (1/9) ;
            }
            if(total == null || total == ''){
                return 0;
            }
            var cover = $("#ryco_toolbar_calculator").data('cover');
            var boxed = Math.ceil(total/parseFloat(cover));
            if(isNaN(boxed))
                $("#qty").val(1);
            else
                $("#qty").val(boxed);
            return total;
        }
        function calculateResult(total, includeTax) {
            // switch between "m" and "yd" (different than m and xy)
            if(total == undefined)
                total = false;
            if(includeTax == undefined)
                includeTax = false;
            var isM = $('#ryco_toolbar_calculator .unit_switcher input').is(':checked');
            $(".your_price_wrapper").each(function (key, product) {
                var product = $(product);
                var currencySymbol = product.data('currency-symbol');
                var result = 0;
                // check is include tax option filter is checked
                if (includeTax && product.data('price-inc-tax')) {
                    var priceIncTax = parseFloat(product.data('price-inc-tax')).toFixed(2);
                }
                if (product.data('coverage')) {
                    var coverage = parseFloat(product.data('coverage'))
                        , price = priceIncTax ? priceIncTax : parseFloat(product.data('minprice')).toFixed(2)
                        , required = Math.ceil(total / coverage);
                    var result = parseFloat(required * price).toFixed(2);
                    // transfer m2 to yd2 by / 1.1959
                    // result = isM ? result : parseFloat(result / 1.1959);
                    // result = parseFloat(result / 1.1959);
                }
                if (result > 0) {
                    product.find('span.your_price').text(getFormattedPrice(result, currencySymbol)).show();
                    product.show();
                } else {
                    product.find('span.your_price').hide();
                    product.hide();
                    product.parent('.product-item-details').addClass("product-item-details--no-price-wrapper");
                }
            });
        }

        function getFormattedPrice(price, currencySymbol) {
            var format = priceUtils.formatPrice(price, {
                decimalSymbol: '.',
                groupLength: 3
            }, false);
            if (!format.includes(currencySymbol, 0)) {
                format = currencySymbol + format;
            }
            return format;
        }

    })(jQuery);
});