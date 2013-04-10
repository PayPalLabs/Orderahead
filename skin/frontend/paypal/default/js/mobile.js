/**
 * PayPal JavaScript
 *
 * @since        :  Magento 1.7
 * @author       :  Paypal - http://www.paypal.com
 * @copyright    :  Copyright (C) 2013 Powered by Paypal
 * @license      :  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @Creation Date:  19/03/2013
 * 
 */
var runOnce = 0;

function getRandomNumber(){
    return new Date().getTime()*2;
}

function gotoURL(urlStr) {
    document.location = urlStr;
}

function forwardURL(urlStr) {
    var urlSymbol = "?";
    if ( urlStr.indexOf("?") >=0 ) {
        urlSymbol = "&"
    }
    $.mobile.changePage(urlStr+urlSymbol+"s="+getRandomNumber(), {
        transition: "slide", 
        reverse: false
    });
    $(".ui-loader").css("display","block");
}

function backwardURL(urlStr) {
    var urlSymbol = "?";
    if ( urlStr.indexOf("?") >=0 ) {
        urlSymbol = "&"
    }
    $.mobile.changePage(urlStr+urlSymbol+"s="+getRandomNumber(), {
        transition: "slide", 
        reverse: true
    });
}

function navigationURL(urlStr) {
    toggleNav("close");
    setTimeout("forwardURL('"+urlStr+"')",300);
}

function selectNav(num) {
    for ( var i=0; i<4; i++ ) {
        var navObj = $('#menuNav_'+i);
        if ( num == i ) {
            navObj.attr('class', 'menu-bar-item selected');
        } else {
            navObj.attr('class', 'menu-bar-item');
        }
    }
}

var menuTransition = 0;

function toggleNav(state) {
	if ( menuTransition == 0 ) {
		if ( !state ) {
			if ( $('.ui-page-active').css('left') == "0px" ) {
				menuTransition = 1;
				$('.ui-page-active').animate({left: "+="+$('#nav').width()}, 200, function() {
					menuTransition = 0;
				});
			} else {
				menuTransition = 1;
				$('.ui-page-active').animate({left: "-="+$('#nav').width()}, 200, function() {
					menuTransition = 0;
				});
			}
		} else {
			if ( state == "open" ) {
				if ( $('.ui-page-active').css('left') == "0px" ) {
					menuTransition = 1;
					$('.ui-page-active').animate({left: "+="+$('#nav').width()}, 200, function() {
						menuTransition = 0;
					});
				}
			} else {
				if ( $('.ui-page-active').css('left') == $('#nav').width()+"px" ) {
					menuTransition = 1;
					$('.ui-page-active').animate({left: "-="+$('#nav').width()}, 200, function() {
						menuTransition = 0;
					});
				}
			}
		}
	}
}

function orientationHandler() {
    if(event.orientation){
        if(event.orientation == 'portrait'){
            resizeContent();
        } else if(event.orientation == 'landscape') {
            resizeContent();
        }
    }
}

function resizeContent() {
    var offset = 55;
    var h = $(document).height();
    $('.ui-loader').css('height',h);
    $('#wrapper').css('height',h);
    $('#category-scroll').css('height',h-offset);
    $('.content-box').css('height',h-offset);
    $('.homepage-box').css('height',h-offset-160);
    $('.product-info-box').css('height',h);
}

var delay = (function(){
    var timer = 0;
    return function(callback, ms){
        clearTimeout (timer);
        timer = setTimeout(callback, ms);
    };
})();

function openPopup(id) {
    var h = $(document).height();
    $('#popupMask').css('height',h);
    $('#popup').css('height',h);
    $('#popupMask').slideDown(300,function() {
        $('#popup').fadeIn(200,function() {
            loadMap(id);
        });
    });
}

function closePopup() {
    $('#popup').fadeOut(200,function() {
        $('#popupMask').slideUp(300,function() {
            unloadMap();
        });
    });
}

function loadMap(id) {
    $("#map").load(function (){
    	$("#map").attr('height','280px');
    });

    $('#map').attr("src",storeRootUrl + "/locationhere/onepage/storemaptemplate/order_id/"+id);
}

function unloadMap() {
    $('#map').attr("src","");
}


// Cart page - update price when change quantity
function updateprice(qid) {	

    var rqid = qid.split('_');
    rid = rqid[0];
    id = rqid[0]+rqid[1];
	
    var oldqty = $('#oldqty'+id).val();
    var newqty = $('#qty'+id).val();
	
    var numprice = $('#item_price'+id).html().replace('$', '');				
    var numsubtotal = $('#subtotal'+rid).html().replace('$', ''); 
    var numtotal = $('#total'+rid).html().replace('$', ''); 	

    // get new amount
    var qty2 = + Number(newqty - oldqty);
    var numprice2 = Math.abs(qty2) * numprice;
			
    // minus / plus amount
    if (oldqty < newqty) {
        newSubtotal = Number(numsubtotal) + Number(numprice2);		
        newTotal = Number(numtotal) + Number(numprice2);  	
    } else {
        newSubtotal = Number(numsubtotal) - Number(numprice2);		
        newTotal = Number(numtotal) - Number(numprice2);	
    }

    newSubtotal = newSubtotal.toFixed(2);
    newTotal = newTotal.toFixed(2);
	
    $('#subtotal'+rid).html('$' + newSubtotal );	
    $('#total'+rid).html( '$' + newTotal );
    $('#oldqty'+id).val(newqty);
 	
}
	
// Events Monitors
$('div').live('pagebeforeshow',function(event, ui) {
    resizeContent();
});

$(window).bind('orientationchange', orientationHandler);

window.onresize = resizeContent;

$(window).resize(function() {
    delay(function(){
        resizeContent();
    }, 500);
});

$(document).bind("pageinit", function(event) {
    if ( runOnce == 0 ) {
        $("#nav").bind("swipeleft",function(event) {
            toggleNav("close");
        });
        runOnce++;
    }
	
    $(".content-box").unbind("swiperight");
    $(".content-box").bind("swiperight",function(event) {
        toggleNav("open");
    });
	
    $(".homepage-box").unbind("swiperight");
    $(".homepage-box").bind("swiperight",function(event) {
        toggleNav("open");
    });
	
    $(".content-box").unbind("swipeleft");
    $(".content-box").bind("swipeleft",function(event) {
        toggleNav("close");
    });
});

function formatCurrency(settings, num){

    // initialize defaults
    var defaults = {
        name: "formatCurrency",
        colorize: false,
        region: '',
        global: true,
        roundToDecimalPlace: 2, // roundToDecimalPlace: -1; for no rounding; 0 to round to the dollar; 1 for one digit cents; 2 for two digit cents; 3 for three digit cents; ...
        eventOnDecimalsEntered: false
    };
            
    // initialize default region
    defaults = $.extend(defaults, $.formatCurrency.regions['']);
    // override defaults with settings passed in
    settings = $.extend(defaults, settings);
 
    //identify '(123)' as a negative number
    if (num.search('\\(') >= 0) {
        num = '-' + num;
    }
    if (num === '' || (num === '-' && settings.roundToDecimalPlace === -1)) {
        return;
    }
 
    // if the number is valid use it, otherwise clean it
    if (isNaN(num)) {
        // clean number
        num = num.replace(settings.regex, '');
                                                               
        if (num === '' || (num === '-' && settings.roundToDecimalPlace === -1)) {
            return;
        }                                                   
        if (settings.decimalSymbol != '.') {
            num = num.replace(settings.decimalSymbol, '.');  // reset to US decimal for arithmetic
        }
        if (isNaN(num)) {
            num = '0';
        }
    }
                                               
    // evalutate number input
    var numParts = String(num).split('.');
    var isPositive = (num == Math.abs(num));
    var hasDecimals = (numParts.length > 1);
    var decimals = (hasDecimals ? numParts[1].toString() : '0');
    var originalDecimals = decimals;
                                               
    // format number
    num = Math.abs(numParts[0]);
    num = isNaN(num) ? 0 : num;
    if (settings.roundToDecimalPlace >= 0) {
        decimals = parseFloat('1.' + decimals); // prepend "0."; (IE does NOT round 0.50.toFixed(0) up, but (1+0.50).toFixed(0)-1
        decimals = decimals.toFixed(settings.roundToDecimalPlace); // round
        if (decimals.substring(0, 1) == '2') {
            num = Number(num) + 1;
        }
        decimals = decimals.substring(2); // remove "0."
    }
    num = String(num);
 
    if (settings.groupDigits) {
        for (var i = 0; i < Math.floor((num.length - (1 + i)) / 3); i++) {
            num = num.substring(0, num.length - (4 * i + 3)) + settings.digitGroupSymbol + num.substring(num.length - (4 * i + 3));
        }
    }
 
    if ((hasDecimals && settings.roundToDecimalPlace == -1) || settings.roundToDecimalPlace > 0) {
        num += settings.decimalSymbol + decimals;
    }
 
    // format symbol/negative
    var format = isPositive ? settings.positiveFormat : settings.negativeFormat;
    var money = format.replace(/%s/g, settings.symbol);
    money = money.replace(/%n/g, num);
 
    return money;
}
	

function initFlex(id) {
    $('#flexslider_'+id).flexslider({
        animation: "slide",
        animationLoop: false,
        itemWidth: 140,
        itemMargin: 5,
        maxItems: 6,
        start: function(slider){
            $('body').removeClass('loading');
        }
    });
}