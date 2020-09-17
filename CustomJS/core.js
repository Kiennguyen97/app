define([
    'jquery', 
    'jquery/jquery.cookie', 
    'Magento_Catalog/js/price-utils'], 
    function($, cookie, priceUtils) {
    'use strict';
    var RYCO_Cal_Core = {
        data: {
            cookieName: 'ryco_calculator_details',
            layout: 'm',
            width: 0,
            length: 0,
            total: 0,
            totalMetres: 0,
            unit: 'yd',
            currencySymbol: false,
            defaultForProduct: false,
        },
        elements: {
            body: $('body'),
            areas: {},
        }
    };
    RYCO_Cal_Core.setCookieData = function() {
        if (isNaN(this.data.total)) {
            this.data.total = 0;
        }
        if (this.data.total > 1) {
            this.data.defaultForProduct = false;
            // if (this.data.unit == 'yd') {
            //     this.data.total *= (1/9);
            // }
        }
        var object = {
            layout: this.data.layout,
            width: this.data.width,
            length: this.data.length,
            total: this.data.total,
            unit: this.data.unit,
            defaultForProduct: this.data.defaultForProduct,
        };
        $.cookie(this.data.cookieName, JSON.stringify(object), {
            path: '/',
            domain: window.location.hostname
        });
        this.elements.body.trigger('ryco_cal_cookieSet');
    }
    ;
    RYCO_Cal_Core.getCookieData = function() {
        var object = $.cookie(this.data.cookieName);
        if (object) {
            object = JSON.parse(object);
            this.data.layout = object.layout;
            this.data.total = parseFloat(object.total);
            // if (this.data.unit == 'yd') {
            //     this.data.total = parseFloat ( object.total ) * (1/9);
            // }
            this.data.width = object.width;
            this.data.length = object.length;
            this.data.unit = object.unit;
            if (object.defaultForProduct) {
                this.data.defaultForProduct = object.defaultForProduct;
            } else {
                this.data.defaultForProduct = false;
            }
            if (this.data.total === null || typeof this.data.total === "undefined" || isNaN(this.data.total)) {
                this.data.total = 0;
            }
            this.elements.body.trigger('ryco_cal_cookieGot');
            this.updateInputs(true);
            return true;
        }
        return false;
    }
    ;
    RYCO_Cal_Core.getFormattedPrice = function(price) {
        var format = priceUtils.formatPrice(price, {
            decimalSymbol: '.',
            groupLength: 3
        }, false);
        if (!format.indexOf(this.data.currencySymbol) >= 0) {
            if ($('body').hasClass('catalog-product-view')) {
                format = format;
            } else {
                format = this.data.currencySymbol + format;
            }
        }
        return format;
    }
    ;
    RYCO_Cal_Core.setCurrencySymbol = function(symbol) {
        this.data.currencySymbol = symbol;
    }
    ;
    RYCO_Cal_Core.updateYourPrices = function() {
        $(".your_price_wrapper").each(function(key, product) {
            var product = $(product)
              , price = RYCO_Cal_Core.calculateYourPrice(product);
            if (price > 0) {
                product.find('span.your_price').text(RYCO_Cal_Core.getFormattedPrice(price)).show();
                product.show();
            } else {
                product.find('span.your_price').hide();
                product.hide();
                product.parent('.product-item-details').addClass("product-item-details--no-price-wrapper");
            }
        });
         // IT-12: Show hide price by unit
         if(this.data.unit === 'yd'){
            $('.price-m').hide();
            $('.price-yd').show();
        }else{
            $('.price-m').show();
            $('.price-yd').hide();
        }   
    }
    ;
    RYCO_Cal_Core.calculateYourPrice = function(product) {
        if (this.data.totalMetres == 0) {
            return 0;
        }
        // check is include tax option filter is checked
        var isPriceIncTax = $('.set_price_tax .include_tax').hasClass('active');
        if (isPriceIncTax && product.data('price-inc-tax')) {
            var priceIncTax = parseFloat(product.data('price-inc-tax')).toFixed(2);
        }
        if (product.data('coverage')) {
            var coverage = parseFloat(product.data('coverage'))
              , price = priceIncTax ? priceIncTax : parseFloat(product.data('minprice')).toFixed(2)
              , required = Math.ceil(this.data.totalMetres / coverage);
            if (product.parent('li').find('input.qty').data('manual') !== true) {
                product.parent('li').find('input.qty').val(required);
            } else {
                product.parent('li').find('input.qty').data('manual', false);
            }
            return parseFloat(required*price).toFixed(2);
        }
        return 0;
    }
    ;
    RYCO_Cal_Core.updateProductPrice = function() {
        var price = this.calculateProductPrice();
        if (!price) {
            return false;
        }
        this.elements.areas.product.wrapper.find('.pricing p.price .value').text(this.getFormattedPrice(price));
    }
    ;
    RYCO_Cal_Core.calculateProductPrice = function() {
        if (!this.data.product) {
            return false;
        }
        if (this.data.totalMetres === 0) {
            this.elements.areas.product.wrapper.find('.pricing').hide();
            return false;
        }
        var coverage = parseFloat(this.data.product.coverage)
          , exact = (this.data.totalMetres % coverage).toFixed(2) == 0.00
          , required = exact ? (this.data.totalMetres / coverage).toFixed(0) : Math.ceil(this.data.totalMetres / coverage)
          , totalCoverage = parseFloat(required * coverage).toFixed(2);
          var price;
          if(!$('.prices-tier-switcher span#inc_vat').hasClass('active')){
              price = parseFloat(this.data.product.minPrice);
          }else{
              price = parseFloat(this.data.product.maxPrice);
          }
        price = parseFloat(price).toFixed(2);
        var totalPrice = parseFloat(required * price).toFixed(2);
        if (this.elements.body.find('input[name="qty"]').data('manual') !== true) {
            this.elements.body.find('input[name="qty"]').val(required);
        } else {
            this.elements.body.find('input[name="qty"]').data('manual', false);
        }
        this.elements.areas.product.wrapper.find('.pricing span.count').text(required);
        if (required == 1) {
            this.elements.areas.product.wrapper.find('.pricing span.plural').hide();
        } else {
            this.elements.areas.product.wrapper.find('.pricing span.plural').show();
        }
        this.elements.areas.product.wrapper.find('.pricing span.coverage').text(totalCoverage);
        this.elements.areas.product.wrapper.find('.pricing').show();
        return totalPrice;
    }
    ;
    RYCO_Cal_Core.calculateMetres = function(object, event, metres) {
        var area = object.data('area');
        if (typeof metres === "undefined") {
            if (this.data.layout === "xy") {
                this.data.width = parseFloat(this.elements.areas[area].inputs.width.val());
                this.data.length = parseFloat(this.elements.areas[area].inputs.length.val());
                // if (this.data.length > 0 && this.data.width > 0) {
                if (!isNaN(this.data.length) && !isNaN(this.data.width)) {
                    // custom recalculate if there are more than 1 length and width input
                    // formula: (length * width) + (length * width)
                    var n = 1;
                    var widthArr = {};
                    // specify layout_xy element to calculate total (change from 'inputs')
                    $('#ryco_toolbar_calculator .layout_xy input[name="width"]').each(function(i, obj) {
                        widthArr[n] = parseFloat($(obj).val());
                        n++;
                    });
                    var n = 1;
                    var lengthArr = {};
                    $('#ryco_toolbar_calculator .layout_xy input[name="length"]').each(function(i, obj) {
                        lengthArr[n] = parseFloat($(obj).val());
                        n++;
                    });
                    var total = parseInt(0);
                    if (n != 1) {
                        for (var i = 1; i < n; i++) {
                            total += parseFloat(widthArr[i]) * parseFloat(lengthArr[i]);
                        }
                        this.data.total = total;
                    } else {
                        this.data.total = parseFloat(this.data.length) * parseFloat(this.data.width);
                    }
                } else {
                    this.data.total = 0;
                }
            } else {
                this.data.width = 0;
                this.data.length = 0;
                this.data.total = parseFloat(this.elements.areas[area].inputs.total.val());
            }
        } else {
            this.data.total = parseFloat(metres);
        }
        if (isNaN(this.data.total)) {
            this.data.total = 0;
        }
        this.data.totalMetres = this.data.total;
        if (this.data.unit == "m" && this.data.totalMetres > 0) {
            this.data.totalMetres = parseFloat((this.data.total * 1.1959));
        }else if(this.data.unit == 'yd' && this.data.totalMetres > 0){
            this.data.totalMetres = parseFloat((this.data.total * (1/9)));
        }
        this.elements.body.trigger('ryco_cal_calculated');
    }
    ;
    RYCO_Cal_Core.setElements = function(layout, elements) {
        this.elements.areas[layout] = elements;
        this.elements.body.trigger('ryco_cal_elementsSet');
        return true;
    }
    ;
    RYCO_Cal_Core.updateInputs = function(fromCookie) {
        for (var area in this.elements.areas) {
            var inputs = this.elements.areas[area].inputs;
            inputs.width = $("#ryco_"+area+"_calculator .inputs input[name='width'],.catalog-product-view #ryco_toolbar_calculator .xy_div input[name='width']");
            inputs.length = $("#ryco_"+area+"_calculator .inputs input[name='length'],.catalog-product-view #ryco_toolbar_calculator .xy_div input[name='length']");
            for (var input in inputs) {
                if (inputs[input].data('manual') !== true) {
                    // prevent set input xy to 0 when loading from cookie with xyLabel, mLabel
                    if(this.data.layout == 'm'&&!fromCookie){
                        $('#ryco_'+area+'_calculator .layout_xy input').each(function(){
                            $(this).val(0);
                            RYCO_Cal_Core.data.width = 0;
                            RYCO_Cal_Core.data.length = 0;
                        })
                    }
                    else{
                        if(input == 'total'){
                            inputs[input].val(this.data[input]);
                            if(fromCookie){
                                $('.catalog-product-view #qty-total').val(this.data[input]);
                                if(this.data.layout == 'm' && this.data.unit == 'yd'){
                                    var result = parseFloat(this.data[input]);
                                    result /= 9;
                                    $('.catalog-product-view #qty-total').val(result.toFixed(2));
                                }
                            }
                        }
                        if(fromCookie){
                            inputs[input].val(this.data[input]);
                        }
                    }
                } else {
                    inputs[input].data('manual', false);
                }
            }
            var unitSwitcher = this.elements.areas[area].unitSwitcher.find('input');
            if (unitSwitcher && this.data.unit === 'm') {
                unitSwitcher[0].checked = false;
            } else {
                unitSwitcher[0].checked = true;
            }
            if(this.data.width == 0 && this.data.length == 0 && fromCookie && this.data.total != 0){
                $('.catalog-product-view #qty-total').attr('data-first-loaded','true');
                $(".catalog-product-view #ryco_toolbar_calculator .xy_div input[name='width']").val('');
                $(".catalog-product-view #ryco_toolbar_calculator .xy_div input[name='length']").val('');
            }
            if (fromCookie) {
                unitSwitcher.trigger('change');
            }
            this.updateUnitPosition(this.elements.areas[area].inputs.total);
            if(this.data.unit == 'm' && fromCookie && (this.data.width == 0 && this.data.length == 0)){
                $('.catalog-product-view #qty-total').attr('data-unit-metres','true');
            }                           
        }
    }
    ;
    RYCO_Cal_Core.updateLayout = function(object, event) {
        var layout = object.val();
        this.data.layout = layout;
        for (let area in this.elements.areas) {
            let layouts = this.elements.areas[area].layouts;
            var layoutObject = this.elements.areas[area].layoutSwitcher.find('input[name="layout_type"][value="' + layout + '"]');
            if (layoutObject !== object) {
                layoutObject.click();
            }
            layouts.wrapper.find("div[class*='layout_']").hide().removeClass('active');
            layouts.wrapper.find(".layout_" + layout).show().addClass('active');
        }
        this.elements.body.trigger('ryco_cal_layoutUpdated');
        return false;
    }
    ;
    RYCO_Cal_Core.updateUnit = function(object, event) {
        event.preventDefault();
        var unit = 'm';
        if (object[0].checked === true) {
            unit = 'yd';
        }
        this.data.unit = unit;
        for (let area in this.elements.areas) {
            var unitObject = this.elements.areas[area].unitSwitcher.find('input');
            if (unitObject !== object) {
                unitObject[0].checked = object[0].checked;
            }
            this.calculateMetres(this.elements.areas[area].wrapper, event);
        }
        this.setCookieData();
        this.elements.body.trigger('ryco_cal_unitUpdated');
        var productInputWith = $(".catalog-product-view #ryco_toolbar_calculator .xy_div input[name='width']").val();
        var productInputLength = $(".catalog-product-view #ryco_toolbar_calculator .xy_div input[name='length']").val();
        if(productInputLength !== '' && productInputWith !== ''){
            $('#ryco_toolbar_calculator .layouts input[name="width"]').trigger('change');
        }
        return false;
    }
    ;
    RYCO_Cal_Core.validateLengthAndWidth = function() {
        // validate the input check is required
        var result = true;
        for (let area in this.elements.areas) {
            var isXyArea = $(this.elements.areas[area].layouts.xy).hasClass('active');
            if(isXyArea){
                // specify layout_xy element to calculate total (change from 'inputs')
                $('#ryco_toolbar_calculator .layout_xy input[name="width"]').each(function(i, obj) {
                    var NaN = isNaN(parseFloat($(obj).val()));
                    if (NaN) {
                        $(obj).addClass('is_required');
                        result = false;
                    } else {
                        $(obj).removeClass('is_required');
                    }
                });
                // specify layout_xy element to calculate total (change from 'inputs')
                $('#ryco_toolbar_calculator .layout_xy input[name="length"]').each(function(i, obj) {
                    var NaN = isNaN(parseFloat($(obj).val()));
                    if (NaN) {
                        $(obj).addClass('is_required');
                        result = false;
                    } else {
                        $(obj).removeClass('is_required');
                    }
                });
            }else{
                $('#ryco_toolbar_calculator .layouts input[name="total"]').each(function(i, obj) {
                    var NaN = isNaN(parseFloat($(obj).val()));
                    if (NaN) {
                        $(obj).addClass('is_required');
                        result = false;
                    } else {
                        $(obj).removeClass('is_required');
                    }
                });
            }
        }
        return result;
    };
    RYCO_Cal_Core.calculateResult = function(obj) {
        if (obj.hasClass('xy')) {
            // update result in area
            // formula: (length * width) + (length * width)
            var n = 1;
            var widthArr = {};
            $('#ryco_toolbar_calculator .layout_xy input[name="width"]').each(function(i, obj) {
                widthArr[n] = parseFloat($(obj).val());
                n++;
            });
            var n = 1;
            var lengthArr = {};
            $('#ryco_toolbar_calculator .layout_xy input[name="length"]').each(function(i, obj) {
                lengthArr[n] = parseFloat($(obj).val());
                n++;
            });
            var total = parseInt(0);
            for (var i = 1; i < n; i++) {
                total += parseFloat(widthArr[i]) * parseFloat(lengthArr[i]);
            }
            if (isNaN(total)) {
                $('#ryco_toolbar_result .ryco_toolbar_output input').val(0);
            } else {
                var isM = $('#product_unit_switcher').is(':checked');
                if(!isM)
                    total = total*1.1959;
                else
                    total = total/9;
                $('#ryco_toolbar_result .ryco_toolbar_output input').val(total.toFixed(2));
            }
        } else {
            var total = parseFloat($('#ryco_toolbar_calculator .inputs input[name="total"]').val());
            if(isNaN(total))
                total = 0;
            var isM = $('#product_unit_switcher').is(':checked');
                if(!isM)
                    total = total*1.1959;
                else
                    total = total / 9;
            if($('#ryco_toolbar_calculator input.total').is(':visible'))
            {
                $('#ryco_toolbar_result .ryco_toolbar_output input').val(total.toFixed(2)); 
            }
        }
    };
    RYCO_Cal_Core.setup = function(product, callback) {
        if (Object.keys(this.elements.areas).length === 0) {
            console.log('no areas provided');
            return false;
        }
        if (typeof product !== "undefined" || product !== null) {
            this.data.product = product;
        }
        for (var area in this.elements.areas) {
            var theArea = this.elements.areas[area];
            $(theArea.calculate).on('click', function(e) {
                e.preventDefault();
                var validated = RYCO_Cal_Core.validateLengthAndWidth();
                if (validated) {
                    RYCO_Cal_Core.calculateMetres($(this), e);
                }
            });
            // custom when click on parent of the "update prices" button, will also run to calculateMetres() function
            $(theArea.calculate.parent()).on('click', function(e) {
                e.preventDefault();
                var validated = RYCO_Cal_Core.validateLengthAndWidth();
                if (validated) {
                    RYCO_Cal_Core.calculateMetres($(this).find('a'), e);
                    // RYCO_Cal_Core.elements.body.trigger('ryco_cal_calculated');
                }
            });
            $(theArea.layoutSwitcher).find('input').on('change', function(e) {
                RYCO_Cal_Core.updateLayout($(this), e);
                // custom add class active when clicked
                $(' #ryco_toolbar_calculator .layout_switcher label').removeClass('active');
                $(this).parent().find('label').addClass('active');
                // custom hide "add new area" when click on other than "xy"
                if ($(this).hasClass('xy')) {
                    $('.ryco-toolbar__inputs label.add_area').show();
                    $('#ryco_toolbar_result .ryco_toolbar_result_span').show();
                    $('.unit_switcher .hasAddArea').hide();
                } else {
                    $('.ryco-toolbar__inputs label.add_area').hide();
                    $('#ryco_toolbar_result .ryco_toolbar_result_span').hide();
                    $('.unit_switcher .hasAddArea').show();
                }
                RYCO_Cal_Core.calculateResult($(this));
            });
            $(theArea.unitSwitcher).find('input').on('change', function(e) {
                RYCO_Cal_Core.updateUnit($(this), e);
                // RYCO_Cal_Core.elements.body.trigger('ryco_cal_calculated');
            });
            if (area === 'product') {
                if (theArea.inputs.width) {
                    $(theArea.inputs.width).on('keyup', function() {
                        $(theArea.calculate).trigger('click');
                    });
                }
                if (theArea.inputs.length) {
                    $(theArea.inputs.length).on('keyup', function() {
                        $(theArea.calculate).trigger('click');
                    });
                }
                if(theArea.inputs.total){
                    $(theArea.inputs.total).on('keyup', function() {
                        $(theArea.calculate).trigger('click');
                    });
                }
            }
            $(theArea.inputs.total).on('keyup change', function(e) {
                var value = $(this).val();
                if (value.length > 7) {
                    $(this).val(value.substring(0, 7)).trigger('change');
                } else {
                    $(this).data('manual', true);
                    RYCO_Cal_Core.updateUnitPosition($(this));
                    RYCO_Cal_Core.calculateMetres($(theArea.calculate), e, value);
                    var isYd = $('#product_unit_switcher').is(':checked');
                    if(!isYd){
                        $('#ryco_toolbar_result .ryco_toolbar_output input').val((parseFloat($(this).val()*1.1959)).toFixed(2));
                    }else{
                        // $('#ryco_toolbar_result .ryco_toolbar_output input').val(parseFloat($(this).val()).toFixed(2));
                        //IT-27
                        $('#ryco_toolbar_result .ryco_toolbar_output input').val((parseFloat($(this).val()*(1/9))).toFixed(2));
                    }
                }
                RYCO_Cal_Core.setCookieData();
            });
        }
        this.elements.body.on('ryco_cal_calculated', function(e) {
            RYCO_Cal_Core.updateInputs();
            RYCO_Cal_Core.updateYourPrices();
            RYCO_Cal_Core.updateProductPrice();
            RYCO_Cal_Core.setCookieData();
        });
        this.elements.body.on('ryco_cal_setup', function(e) {
            let firstLayout = Object.keys(RYCO_Cal_Core.elements.areas)[0];
            RYCO_Cal_Core.elements.areas[firstLayout].layoutSwitcher.find('input[value="' + RYCO_Cal_Core.data.layout + '"]').click().trigger('change');
            RYCO_Cal_Core.elements.areas[firstLayout].unitSwitcher.find('input[value="' + RYCO_Cal_Core.data.unit + '"]').click().trigger('change');

            for (let area in RYCO_Cal_Core.elements.areas) {
                RYCO_Cal_Core.elements.areas[area].wrapper.show();
                RYCO_Cal_Core.calculateMetres(RYCO_Cal_Core.elements.areas[area].wrapper, e);
            }
        });
        this.elements.body.on('ryco_cal_unitUpdated', function(e) {
            for (let area in RYCO_Cal_Core.elements.areas) {
                var placeholder = RYCO_Cal_Core.elements.areas[area].wrapper.find('label.total-placeholder');
                if (placeholder.length > 0) {
                    // placeholder.html(RYCO_Cal_Core.data.unit + "<sup>2</sup>);
                    //IT-27 
                    if (RYCO_Cal_Core.data.unit == 'yd') {
                        placeholder.html('ft<sup>2</sup>');
                    }else {
                        placeholder.html(RYCO_Cal_Core.data.unit + "<sup>2</sup>");
                    }
                }
                var placeholder1 = RYCO_Cal_Core.elements.areas[area].wrapper.find('label.total-placeholder-1');
                var placeholder2 = RYCO_Cal_Core.elements.areas[area].wrapper.find('label.total-placeholder-2');
                if (placeholder1.length > 0) {
                    // placeholders1.html(RYCO_Cal_Core.data.unit);
                    //IT-27 
                    if (RYCO_Cal_Core.data.unit == 'yd') {
                        placeholder1.html('ft');
                    }else {
                        placeholder1.html(RYCO_Cal_Core.data.unit);
                    }
                }
                if (placeholder2.length > 0) {
                    // placeholders2.html(RYCO_Cal_Core.data.unit);
                    //IT-27 
                    if (RYCO_Cal_Core.data.unit == 'yd') {
                        placeholder2.html('ft');
                    }else {
                        placeholder2.html(RYCO_Cal_Core.data.unit);
                    }
                }
                var placeholders1 = $('.product-type-attribute .layouts').find('label.total-placeholder-1');
                var placeholders2 = $('.product-type-attribute .layouts').find('label.total-placeholder-2');
                if (placeholders1.length > 0) {
                    // placeholders1.html(RYCO_Cal_Core.data.unit);
                    //IT-27 
                    if (RYCO_Cal_Core.data.unit == 'yd') {
                        placeholders1.html('ft');
                    }else {
                        placeholders1.html(RYCO_Cal_Core.data.unit);
                    }
                }
                if (placeholders2.length > 0) {
                    // placeholders2.html(RYCO_Cal_Core.data.unit);
                    //IT-27 
                    if (RYCO_Cal_Core.data.unit == 'yd') {
                        placeholders2.html('ft');
                    }else {
                        placeholders2.html(RYCO_Cal_Core.data.unit);
                    }
                }
            }
        });
        this.getCookieData();
        if (typeof callback !== "undefined") {
            callback();
        }
        this.elements.body.trigger('ryco_cal_setup');
        //TODO: fix switch
        if(RYCO_Cal_Core.data.unit==='m'){
            // $('.product-info-price .unit-switcher .unit-m').trigger('click');
            $('#product_unit_switcher').trigger('change');
            $('.info-price-wrapper .collect-deliver li.unit-m #unit-f-option1').attr('checked', 'checked');
        }else{
            $('.info-price-wrapper .collect-deliver li.unit-yards #unit-f-option2').attr('checked', 'checked');
        }
    }
    ;
    $('input[name="qty"]').on('keyup keydown', function(e) {
        if (e.keyCode == "13") {
            e.preventDefault();
            return false;
        }
    });
    $('input[name="qty"]').on('change', function(e) {
        let boxes = $(this).val();
        if (boxes < 1) {
            $(this).val(1).trigger('change');
            return false;
        }
        var metres = null;
        if (RYCO_Cal_Core.data.product) {
            metres = parseFloat(boxes * RYCO_Cal_Core.data.product.coverage).toFixed(2);
            $(this).data('manual', true);
            RYCO_Cal_Core.updateUnitPosition($(this));
            let firstLayout = Object.keys(RYCO_Cal_Core.elements.areas)[0];
            RYCO_Cal_Core.calculateMetres(RYCO_Cal_Core.elements.areas[firstLayout].wrapper, e, parseFloat(metres));
        }
    });
    RYCO_Cal_Core.updateUnitPosition = function(input) {
        var length = input.val().length, 
            html = 'Total Area';
        if (length !== 0) {
            if(RYCO_Cal_Core.data.unit == 'yd'){
                html = 'ft<sup>2</sup>';
            }else{
                html = RYCO_Cal_Core.data.unit + '<sup>2</sup>';
            }
        }
        $('.total-placeholder').html(html);
    };
    
    $(document).on("click", "[for='ryco-cal-m']", function() {
        RYCO_Cal_Core.updateUnitPosition($(".layout_m input"));
    });
    return RYCO_Cal_Core;
});



// jljljj