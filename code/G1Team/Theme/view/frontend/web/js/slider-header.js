define([
  'jquery',
    'owlcarouselslider'
], function ($) {
  'use strict';
  $('#sliderheader').owlCarousel({
    nav: true,
    items: 1,
    navText: ["<i class='fas fa-chevron-left'></i>", "<i class='fas fa-chevron-right'></i>"]
  });
  $('#sliderheader').on('changed.owl.carousel', function (e) {
    restart();
    var item = e.item.index + 1;
    $('.count-item').html(item);
    // var currentItem = event.item.index;
    // window.location.hash = currentItem + 1;
  });
  $(document).ready(restart);
  function restart() {
    $('#sliderheader .item .item-extend').css({ 'right': '-1px' }),
      $('#sliderheader .item .item-extend').css({ 'display': 'none' }),
      $('#sliderheader .item .item-extend').animate({ 'right': '30%' }, { "duration": 3000, "queue": false });
    $('#sliderheader .item .item-extend').fadeIn(2000);
  }

  
});