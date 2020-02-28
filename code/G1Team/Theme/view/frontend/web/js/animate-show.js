define([
    'jquery',

], function ($) {
    'use strict';
    $(document).ready(restart);
    var animateshow = {};
    animateshow.restart = function () {
        $('#sliderheader .item .item-extend').css({ 'right': '-1px' }),
            $('#sliderheader .item .item-extend').css({ 'display': 'none' }),
            $('#sliderheader .item .item-extend').animate({ 'right': '30%' }, { "duration": 3000, "queue": false });
        $('#sliderheader .item .item-extend').fadeIn(2000);
    }
    return animateshow;

});