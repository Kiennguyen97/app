define([
    'jquery',
    'owlcarouselslider'
  ], function ($) {
    'use strict';
    $('#sliderclient').owlCarousel({
      items: 4,
    });
    $('#sliderclient').on('changed.owl.carousel', function (e) {
      var item = e.item.index + 1;
      $('.count-item').html(item);
      // var currentItem = event.item.index;
      // window.location.hash = currentItem + 1;
    });
    
  
    
  });