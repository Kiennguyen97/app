$( document ).ajaxComplete(function() {
    createLabel();
});
/* VBA-87: label product configurable*/
function createLabel(){
    if ($(document).find('.catalog-product-view').length > 0) {
        if ($(document).find('.special-price.has-special').length > 0){
            $('.special-price.has-special').each(function() {
                var specialPrice = $(this).find('.price-wrapper').attr('data-price-amount');
                var oldPrice = $(this).parent().find('.old-price.has-special .price-wrapper').attr('data-price-amount');
                var save =  Math.round (( oldPrice - specialPrice )*100 / oldPrice);
                var html = '';
                if ($(this).parents('.product-item-info.effect').length > 0) {
                    parent = $(this).parents('.product-item-info.effect');
                    if (parent.find('.product-label.sale-label').length == 0) {
                        if (parent.find('.product-label.new-label').length > 0) {
                            html += '<span class="product-label sale-label multiple-label"><span>-'+save+'%</span></span>';
                        }else {
                            html += '<span class="product-label sale-label"><span>-'+save+'%</span></span>';
                        }
                        parent.find('.product-item-image').append(html);
                    }
                }else {
                    parent = $(this).parents('.row-detail-product');
                    if (parent.find('.product-label.sale-label').length == 0) {
                        if (parent.find('.product-label.new-label').length > 0) {
                            html += '<span class="product-label sale-label multiple-label"><span>-'+save+'%</span></span>';
                        }else {
                            html += '<span class="product-label sale-label"><span>-'+save+'%</span></span>';
                        }
                        parent.find('.detail-label').append(html);
                    }
                }
            });
        }
    }else {
        if ($(document).find('.special-price.has-special').length > 0){
            $('.special-price.has-special').each(function() {
                var specialPrice = $(this).find('.price-wrapper').attr('data-price-amount');
                var oldPrice = $(this).parent().find('.old-price.has-special .price-wrapper').attr('data-price-amount');
                var save =  Math.round (( oldPrice - specialPrice )*100 / oldPrice);
                var html = '';
                parent = $(this).parents('.product-item-info.effect');
                if (parent.find('.product-label.sale-label').length == 0) {
                    if (parent.find('.product-label.new-label').length > 0) {
                        html += '<span class="product-label sale-label multiple-label"><span>-'+save+'%</span></span>';
                    }else {
                        html += '<span class="product-label sale-label"><span>-'+save+'%</span></span>';
                    }
                    parent.find('.product-item-image').append(html);
                }
            });
        }
    }
};