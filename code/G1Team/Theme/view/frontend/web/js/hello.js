/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * @api
 */
define([
    'jquery'
], function ($) {
    'use strict';
    /**
     * $.widget('kien.hello', {
     *      options: {
     *         
     *      },
     *      _create: function () {
     *          this.actions = $(this.options.actionsSelector);
     *          // alert("Hello");
     *          $(this.actions).on('click', $.proxy(function () {
     *              alert("Hello");
     *              // $(console.log(config));
     *              // $(console.log(config.content));
     *              // return $('<div></div>').html(config.content).alert(config);
     *          }, this));
     *      }
     *  });
     *  
     *  return $.kien.hello;
     */
    $(window).scroll(function () {
        var currentScrollPos = $(window).scrollTop();
        if (currentScrollPos <= 20) {
            $(".page-header .header.content").removeClass("scroll");
        } else {
            $(".page-header .header.content").addClass("scroll");
        }
    });
});