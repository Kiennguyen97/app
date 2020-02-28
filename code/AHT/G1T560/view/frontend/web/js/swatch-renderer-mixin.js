/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/* eslint-disable max-nested-callbacks, no-undef */
define([
    'jquery',
    'underscore',
    'mage/template',
    'mage/smart-keyboard-handler',
    'mage/translate',
    'priceUtils',
    'loopaddtocart',
    'jquery/ui',
    'jquery/jquery.parsequery',
    'mage/validation/validation'
], function ($, _, mageTemplate, keyboardHandler, $t, priceUtils,loop) {
    'use strict';


    var homeCrumb = function () {
        alert('Hello from SwatchExtend1');
    };

    // return function (swatch) {
    //     alert('Hello from SwatchExtend1');


    //     return swatch;
    // };


    //TODO: 
    


    return function (widget) {
        // alert('Hello from SwatchExtend2');
        // console.log('Hello from SwatchExtenda');
        var lastAttribute;
        var listchild = [];
        
        
        $.widget('mage.SwatchRenderer', widget, {
            // _Rebuild: function () {
            //     console.log('Hello from rebuild methoda');
            //     // homeCrumb();
            //     return this._super();
            // }
            _RenderControls: function () {
                var $widget = this,
                    container = this.element,
                    classes = this.options.classes,
                    chooseText = this.options.jsonConfig.chooseText,
                    countOptions = 0,
                    numberOption = 0,
                    
                    lastAttributeId;
                    
                $widget.optionsMap = {};
                // console.log(this.options);
                $.each(this.options.jsonConfig.attributes, function () {
                    numberOption = numberOption + 1;
                });
                $('[name=number_option]').val(numberOption);
                loop.display();

                // console.log(listchild);
                // $('#product-addtocart-button').on('click', function(e) {
                //     loop.test(listchild,lastAttributeId);
                // });
                // container.append(numberinput);


                // console.log(numberOption);

                $.each(this.options.jsonConfig.attributes, function () {
                    var item = this,
                        controlLabelId = 'option-label-' + item.code + '-' + item.id,
                        input = $widget._RenderFormInput(item),
                        options = $widget._RenderSwatchOptions(item, controlLabelId),
                        // optionlast = $widget._RenderSwatchOptionLast(item, controlLabelId),
                        select = $widget._RenderSwatchSelect(item, chooseText),
                        listLabel = '',
                        firstSpan = '',
                        div = '',
                        subDiv = '',
                        secondSpan = '';

                        lastAttributeId = item.id;
                        // console.log(lastAttributeId);

                    // //TODO: console test
                    // console.log(item); //  object
                    // console.log(this.options.length); // length option cua item

                    // //TODO: dem so option
                    countOptions = countOptions + 1;
                    // console.log(countOptions);

                    // Show only swatch controls
                    if ($widget.options.onlySwatches && !$widget.options.jsonSwatchConfig.hasOwnProperty(item.id)) {
                        return;
                    }
                    //TODO: Default True
                    if ($widget.options.enableControlLabel) {
                        firstSpan = document.createElement('span');
                        secondSpan = document.createElement('span');
                        firstSpan.setAttribute('id', controlLabelId);
                        firstSpan.setAttribute('class', classes.attributeLabelClass);
                        firstSpan.textContent = item.label;
                        secondSpan.setAttribute('class', classes.attributeSelectedOptionLabelClass);
                    }
                    //TODO: Default False
                    if ($widget.inProductList) {
                        $widget.productForm.append(input);
                        input = '';
                        listLabel = document.createAttribute('aria-label');
                        listLabel.value = item.label;
                    } else {
                        listLabel = document.createAttribute('aria-labelledby');
                        listLabel.value = controlLabelId;
                    }

                    div = document.createElement('div');
                    subDiv = document.createElement('div');
                    div.setAttribute('class', classes.attributeClass + ' ' + item.code);
                    div.setAttribute('attribute-code', item.code);
                    div.setAttribute('attribute-id', item.id);
                    div.setAttribute('style','border: 1px solid black; margin-bottom: 10px; padding: 10px;');
                    
                    subDiv.setAttribute('aria-activedescendant', '');
                    subDiv.setAttribute('tabindex', 0);
                    subDiv.setAttribute('aria-invalid', false);
                    subDiv.setAttribute('aria-required', true);
                    subDiv.setAttribute('role', 'listbox');
                    subDiv.setAttributeNode(listLabel);
                    subDiv.setAttribute('class', classes.attributeOptionsWrapper + ' clearfix');
                    if (countOptions === numberOption && numberOption > 1) {
                        subDiv.innerHTML = $widget._RenderSwatchOptionLast(item, controlLabelId,lastAttributeId);
                    } else {
                        div.innerHTML = input;
                        subDiv.innerHTML = options + select;
                    }

                    if ($widget.options.enableControlLabel) {
                        div.appendChild(firstSpan);
                        div.appendChild(secondSpan);
                    }

                    div.appendChild(subDiv);

                    // Create new control
                    container.append(div.outerHTML);

                    $widget.optionsMap[item.id] = {};

                    // Aggregate options array to hash (key => value)
                    $.each(item.options, function () {
                        if (this.products.length > 0) {
                            $widget.optionsMap[item.id][this.id] = {
                                price: parseInt(
                                    $widget.options.jsonConfig.optionPrices[this.products[0]].finalPrice.amount,
                                    10
                                ),
                                products: this.products
                            };
                        }
                    });
                });
                lastAttribute = lastAttributeId
                // Connect Tooltip
                container
                    .find('[option-type="1"], [option-type="2"], [option-type="0"], [option-type="3"]')
                    .SwatchRendererTooltip();

                // Hide all elements below more button
                $('.' + classes.moreButton).nextAll().hide();

                // Handle events like click or change
                $widget._EventListener();

                // Rewind options
                $widget._Rewind(container);

                //Emulate click on all swatches from Request
                $widget._EmulateSelected($.parseQuery());
                $widget._EmulateSelected($widget._getSelectedAttributes());
            },
            _RenderSwatchOptionLast: function (config, controlId,lastAttributeId) {
                var optionConfig = this.options.jsonSwatchConfig[config.id],
                    optionClass = this.options.classes.optionClass,
                    sizeConfig = this.options.jsonSwatchImageSizeConfig,
                    moreLimit = parseInt(this.options.numberToShow, 10),
                    moreClass = this.options.classes.moreButton,
                    moreText = this.options.moreButtonText,
                    countAttributes = 0,
                    html = '',
                    i = 0,
                    numberchild = 0;
                $.each(config.options, function () {
                    numberchild = numberchild + 1;

                });

                $('[name=number_child]').val(numberchild);
                // inputnumberchild = document.createElement('input');
                // inputnumberchild.setAttribute('type','hidden');
                // inputnumberchild.setAttribute('value',numberchild);
                // inputnumberchild.setAttribute('class','number-child');
                // html += inputnumberchild.outerHTML;

                // console.log(config.label + ' render');

                if (!this.options.jsonSwatchConfig.hasOwnProperty(config.id)) {
                    return '';
                }

                $.each(config.options, function () {
                    var id,
                        type,
                        value,
                        thumb,
                        label,
                        width,
                        height,
                        link,
                        subdiv,
                        totaldiv,
                        div;
                    i = i + 1;
                    if (!optionConfig.hasOwnProperty(this.id)) {
                        return '';
                    }
                    
                    // Add more button
                    if (moreLimit === countAttributes++) {
                        link = document.createElement('a');
                        link.setAttribute('class', moreClass);
                        link.setAttribute('href', '#');
                        link.textContent = moreText;

                        html += link.outerHTML;
                    }

                    id = this.id;
                    type = parseInt(optionConfig[id].type, 10);
                    value = optionConfig[id].hasOwnProperty('value') ? optionConfig[id].value : '';
                    thumb = optionConfig[id].hasOwnProperty('thumb') ? optionConfig[id].thumb : '';
                    width = _.has(sizeConfig, 'swatchThumb') ? sizeConfig.swatchThumb.width : 110;
                    height = _.has(sizeConfig, 'swatchThumb') ? sizeConfig.swatchThumb.height : 90;
                    label = this.label ? this.label : '';

                    listchild.push(id);
                    // console.log(listchild);
                    // console.log(id);

                    totaldiv = document.createElement('div');

                    div = document.createElement('div');
                    div.setAttribute('id', controlId + '-item-' + id);
                    div.setAttribute('aria-checked', false);
                    div.setAttribute('aria-describedby', controlId);
                    div.setAttribute('tabindex', 0);
                    div.setAttribute('option-type', type);
                    div.setAttribute('option-id', id);
                    div.setAttribute('option-label', label);
                    div.setAttribute('aria-label', label);
                    div.setAttribute('option-tooltip-thumb', thumb);
                    div.setAttribute('option-tooltip-value', value);
                    div.setAttribute('role', 'option');
                    div.setAttribute('thumb-width', width);
                    div.setAttribute('thumb-height', height);

                    totaldiv.appendChild(div);

                    subdiv = document.createElement('input');

                    subdiv.setAttribute('value', 0);
                    subdiv.setAttribute('class', 'input-text qty-'+id+'');
                    subdiv.setAttribute('type', 'number');
                    subdiv.setAttribute('name', 'super_attribute['+lastAttributeId+']['+id+']');
                    subdiv.setAttribute('style', 'width: 120%; text-align:center');
                    // subdiv.setAttribute('data-validate', '{"required-number":true,"validate-item-quantity":{"minAllowed":1,"maxAllowed":10000}}')


                    totaldiv.appendChild(subdiv);
                    totaldiv.setAttribute('style', 'display:inline-block; width:30px; margin:0 5px;')

                    if (!this.hasOwnProperty('products') || this.products.length <= 0) {
                        div.setAttribute('option-empty', true);
                    }

                    if (type === 0) {
                        // Text
                        div.setAttribute('class', optionClass + ' text');
                        div.textContent = value ? value : label;
                    } else if (type === 1) {
                        // Color
                        div.setAttribute('class', optionClass + ' color');
                        div.setAttribute('style', 'background: ' + value + ' no-repeat center; background-size: initial;');

                    } else if (type === 2) {
                        // Image
                        div.setAttribute('class', optionClass + ' image');
                        div.setAttribute('style',
                            'background: url(' + value +
                            ') no-repeat center;' +
                            ' background-size: initial;' +
                            ' width:' + sizeConfig.swatchImage.width + 'px;' +
                            ' height:' + sizeConfig.swatchImage.height + 'px;');
                    } else if (type === 3) {
                        // Clear
                        div.setAttribute('class', optionClass);
                    } else {
                        // Default
                        div.setAttribute('class', optionClass);
                        div.textContent = label;
                    }
                    // div.appendChild(subdiv);
                    // html += div.outerHTML;
                    html += totaldiv.outerHTML;


                });
                return html;
            },
            _OnClick: function ($this, $widget, eventName) {
                var $parent = $this.parents('.' + $widget.options.classes.attributeClass),
                    $wrapper = $this.parents('.' + $widget.options.classes.attributeOptionsWrapper),
                    $label = $parent.find('.' + $widget.options.classes.attributeSelectedOptionLabelClass),
                    attributeId = $parent.attr('attribute-id'),
                    $input = $parent.find('.' + $widget.options.classes.attributeInput);
                    // console.log($widget.options.classes.attributeInput);
                    // console.log(attributeId);
                    // console.log(lastAttribute);
                    if (attributeId != lastAttribute) {
                        for (let i = 0; i < listchild.length; i++) {
                            $('.qty-'+listchild[i]+'').val(0);
                        }
                    }
                
                if ($widget.inProductList) {
                    $input = $widget.productForm.find(
                        '.' + $widget.options.classes.attributeInput + '[name="super_attribute[' + attributeId + ']"]'
                    );
                }
    
                if ($this.hasClass('disabled')) {
                    return;
                }
    
                if ($this.hasClass('selected')) {
                    $parent.removeAttr('option-selected').find('.selected').removeClass('selected');
                    $input.val('');
                    $label.text('');
                    $this.attr('aria-checked', false);
                } else {
                    $parent.attr('option-selected', $this.attr('option-id')).find('.selected').removeClass('selected');
                    $label.text($this.attr('option-label'));
                    $input.val($this.attr('option-id'));
                    $input.attr('data-attr-name', this._getAttributeCodeById(attributeId));
                    $this.addClass('selected');
                    $widget._toggleCheckedAttributes($this, $wrapper);
                }
    
                $widget._Rebuild();
    
                if ($widget.element.parents($widget.options.selectorProduct)
                    .find(this.options.selectorProductPrice).is(':data(mage-priceBox)')
                ) {
                    $widget._UpdatePrice();
                }
    
                $widget._loadMedia(eventName);
                $input.trigger('change');
            },
           
            
        });

        
        
        return $.mage.SwatchRenderer;
    };

});
