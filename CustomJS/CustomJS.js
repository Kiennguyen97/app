

$(document).ajaxComplete(function () {
  createLabel();
});
// TODO: Replace number 



priceConvert = 'AUD$1,000.00',
						priceText = priceConvert.replace(/[0-9,.]/g, ''),  //AUD$
            priceNumber = priceConvert.replace(/[^\d,.-]/g, ''), //1,000.00
            

// TODO:    DELAY
setTimeout(fixpin, 14000);
setTimeout(function () { createLabel(); }, 10000);
setTimeout(createLabel, 15000);
// goi ten ham khong duoc co dau () khong la chay luon ko timeout


//

if ($('.page-product-giftcard').length > 0) {
  $('.box-tocart #product-addtocart-button').on('click', function (e) {
    setTimeout(addGift, 5000);
  });
}

//  TODO: VO CUC: 
setInterval(function () { alert("Hello"); }, 3000);


var myVar = setInterval(myTimer, 1000);

function myTimer() {
  var d = new Date();
  var t = d.toLocaleTimeString();
  document.getElementById("demo").innerHTML = t;
}

function myStopFunction() {
  clearInterval(myVar);
}
// TODO: check vo cuc
var myVar = setInterval(check, 1000);

function check() {
  if ($(document).find('#VideoWorker-0').length > 0) {
    var iframe = document.getElementById('VideoWorker-0');
    var iframeDoc = iframe.contentDocument;

    // Check if loading is complete
    if (iframeDoc.readyState == 'complete') {
      //iframe.contentWindow.alert("Hello");
      iframe.contentWindow.onload = function () {
        alert("I am loaded");
        clearInterval(myVar);
      };
    }
  }
}

// TODO: Page load
window.onload = function () {
  setTimeout(function () { $('#preload').removeClass('active'); }, 1000);
}
$( window ).load(function() {
  // Run code
});

// TODO: CLONE
jQuery(document).ready(function ($) {

  // Show elements
  jQuery('.store-selector')
    .clone()
    .appendTo(jQuery('.mobile-store-selector'));

  $(document).on('click', '.mobile-store-selector .store-current', function (e) {

    $('.mobile-store-selector .store-dropdown')
      .toggleClass('active');

    e.preventDefault();
    return false;
  });

});

// Prevent
$('.div-dropdown-list-store .current-store span').text(checkSite());
$('.div-dropdown-list-store .dropdown-store-item a').on('click', function (e) {
  e.preventDefault();
  e.stopPropagation();


  var url = $(this).attr('href');
  var nameStore = $(this).text();
  $.cookie("storelistpopup", null, {
    path: '/'
  });
  $.cookie('storelistpopup', nameStore);
  window.location.replace(url);
});

// TODO: OBJECT in Javascript:
// object newPrices
newPrices.finalPrice.amount

$('.page-title-wrapper .breadcrumbs ul.items li a').each(function() {
  url.push($(this).attr('href'));
});


// TODO: foreach array 
checkSpecialPrice: function (item) {
            var item_id = item['item_id'];

            window.checkoutConfig.priceinfo.forEach(myFunction);

            function myFunction(item, index, arr) {
                // arr[index] = item * 10;
                if (item_id == item['item_id'] ) {
                    console.log(item['special']);
                    return 1;
                }
            }
            return 0;
        },

// TODO: autoFill chrome
$('.right-content-portal .block-customer-login .field input').each(function () {

  if ($(this).is(":-webkit-autofill")) 
  {    
    $(this).parent().find('label').addClass('active');
  }else {
    $(this).parent().find('label').removeClass('active');
  }

});


// TODO: TIME DAY

getDay();
        getTime(524);
        

        function getDay () {
            var weekday = ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];
            var d = new Date();
            var date = d.getDate();
            var month = d.getMonth() + 1;
            var year = d.getFullYear()%100;
            var n = d.getDay();
            var dayOfWeek = weekday[n];
            var calendar = date + '/' + month + '/' + year;
            var realTime = $(document).find('.wrapper.wrapper-desktop .real-time');

            realTime.find('.date').text(dayOfWeek);
            realTime.find('.time').text(calendar);
            $(document).find('.wrapper.wrapper-mobile .cart-subtotal-mobile .time').text(calendar);
        }

        function getTime(time){
            var hours = parseInt(time/100);
            var mins = time%100;
            var current = hours+'hrs'+mins+'mins';
            $(document).find('.delivery-order .hours').text(current);
            
            setTimeout(function () {
                time = time%100 == 0 ? ((hours-1)*100 + 59) : (time - 1);
                if (time > 0) {
                    getTime(time)
                }else {
                    $(document).find('.delivery-order .hours').text('0hrs0mins');
                }
            }, 60000);
            
            return 0;
        }