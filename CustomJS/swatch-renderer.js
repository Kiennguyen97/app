/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * BSS Commerce does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * BSS Commerce does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   BSS
 * @package    Bss_ProductStockAlert
 * @author     Extension Team
 * @copyright  Copyright (c) 2015-2017 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
define([
    'jquery',
    'underscore',
    'jquery/ui',
    'jquery/jquery.parsequery'
], function ($, _) {
    'use strict';
    return function (widget) {

		$.widget('mage.SwatchRenderer', widget, {

	        /**
	         * Event for swatch options
	         *
	         * @param {Object} $this
	         * @param {Object} $widget
	         * @private
	         */
	        _OnClick: function ($this, $widget) {

	            $widget._super($this, $widget);

	            $widget._UpdateAdvancedHidePrice($this);
	        },

	        /**
	         * Event for select
	         *
	         * @param {Object} $this
	         * @param {Object} $widget
	         * @private
	         */
	        _OnChange: function ($this, $widget) {
	            
	            $widget._super($this, $widget);

	            $widget._UpdateAdvancedHidePrice();
	        },

	        _UpdateAdvancedHidePrice: function () {
	            var $widget = this,
	                index = '',
	                currentEl = 'currentEl',
	                elPrice,
	                childProductData = this.options.jsonConfig.advancedHidePrice,
	                $useAdvacedPrice,
	                $content;
	            $widget.element.find('.' + $widget.options.classes.attributeClass + '[option-selected]').each(function () {
	                index += $(this).attr('option-selected') + '_';
	            });
				if (typeof childProductData  !== "undefined" ) {

	            if(jQuery('#advancedhideprice').length) { //product page

		            if(!childProductData['child'].hasOwnProperty(currentEl)) {
		            	childProductData['child'][currentEl] = jQuery('#advancedhideprice').html();
		            }

		            if (!childProductData['child'].hasOwnProperty(index)) {
		                $widget._ResetAdvancedHidePrice(childProductData['child'][currentEl]);
		                return false;
		            }
		            $useAdvacedPrice = childProductData['child'][index]['call_hide_price'];
	            	$content = childProductData['child'][index]['call_hide_price_content'];
		            if (!$useAdvacedPrice) {
		                jQuery('.price-box.price-final_price').css('display', 'block');
		                jQuery('#advancedhideprice').html(childProductData['child'][currentEl]);
		            } else {
		                jQuery('.price-box.price-final_price').css('display', 'none');
		                jQuery('#advancedhideprice').html($content);
		            }
		        }else { //category page
		        	if (!childProductData.hasOwnProperty('parent_id')) {
		        		return false;
		        	}

		        	var selector = childProductData['selector'];
	            	var element = '#advancedhideprice_price'+childProductData['parent_id'];

		            if (!childProductData['child'].hasOwnProperty(index)) {
		                $widget._ResetAdvancedHidePriceCategory(childProductData['child'][currentEl], element, selector);
		                return false;
		            }

		            $useAdvacedPrice = childProductData['child'][index]['call_hide_price'];
	            	$content = childProductData['child'][index]['call_hide_price_content'];
	            	
		            if (!$useAdvacedPrice) {
					    jQuery(element).parent().find('.action.tocart').show();
					    jQuery(element).parent().find(selector).show();
					    jQuery(element).parents(".product-item-details").find('.action.tocart').show();
					    jQuery(element).parents(".product-item-details").find(selector).show();
		                jQuery('#advancedhideprice_price'+childProductData['parent_id']).show();
		                jQuery('#advancedhideprice_'+childProductData['parent_id']).html('');
		            } else { 
				        jQuery(element).parent().find('.action.tocart').hide();
				        jQuery(element).parent().find(selector).hide();
				        jQuery(element).parents(".product-item-details").find('.action.tocart').hide();
				        jQuery(element).parents(".product-item-details").find(selector).hide();
				        jQuery('#advancedhideprice_price'+childProductData['parent_id']).hide();
		                jQuery('#advancedhideprice_'+childProductData['parent_id']).html($content);
		            }
				}
			}
				
	        },

	        _ResetAdvancedHidePrice: function (currentEl) {
	            jQuery('.price-box.price-final_price').css('display', 'block');
	            jQuery('#advancedhideprice').html(currentEl);
	        },

	        _ResetAdvancedHidePriceCategory: function (elm, selector) {
	            jQuery(elm).show();
	            jQuery(elm).parent().find('.action.tocart').show();
			    jQuery(elm).parent().find(selector).show();
			    jQuery(elm).parents(".product-item-details").find('.action.tocart').show();
			    jQuery(elm).parents(".product-item-details").find(selector).show();
			    jQuery(elm).prev().html('');
	        },
	    });

    	return $.mage.SwatchRenderer;
    }
});
