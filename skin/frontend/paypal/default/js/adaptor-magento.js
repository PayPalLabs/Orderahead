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
function getNavigationJSON() {
    $.getJSON(storeRootUrl + "jsoncatalog/index/navigation", function(jsonObj) {
        renderNavigation(jsonObj);
    });
}

function getHomepageJSON(sid) {
    $.getJSON(storeRootUrl + "jsoncatalog/index/navigation", function(jsonObj) {
        renderHomepage(jsonObj,sid);
    });
}

function getHomepageFeaturedJSON(sid) {
    $.getJSON(storeRootUrl + "paypal_featuredcategory/category/list", function(jsonObj) {
        renderHomepageFeaturedList(jsonObj,sid);
    });
}

function getProductListJSON(id,sid) {
    $.getJSON(storeRootUrl + "jsoncatalog/product/list/id/"+id, function(jsonObj) {
        renderProductList(jsonObj,sid);
    });
}

function getProductInfoJSON(id,sid) {
    $.getJSON(storeRootUrl + "jsoncatalog/product/view/id/"+id, function(jsonObj) {
        renderProductInfo(jsonObj,sid);
    });
}

function getFavoriteListJSON(id) {
    $.getJSON(storeRootUrl + "favorite/index/view", function(jsonObj) {
        renderFavoriteList(jsonObj,id);
    });
}

function  getCartPageJSON(id) {
    $.getJSON(storeRootUrl+"/jsoncheckout/cart/index", function(jsonObj) {
        renderCartPage(jsonObj,id);
    });
}

function  getConfirmPageJSON(id) {
    $.getJSON(storeRootUrl+"/jsoncheckout/onepage/success", function(jsonObj) {
        renderConfirmPage(jsonObj,id);
    });
}

function  getRecentOrderPageJSON(id) {
    $.getJSON(storeRootUrl+"/paypalsales/order/recent", function(jsonObj) {
        renderRecentOrderPage(jsonObj,id);
    });
}


function  getOrderInfoPageJSON(id, sid) {
    $.getJSON(storeRootUrl+"/paypalsales/order/details/order_id/" + id, function(jsonObj) {
        renderOrderInfoPage(jsonObj,sid);
    });
}

// Recent Order Page JSON Functions
function renderRecentOrderPage(jsonObj,id) {
    var dataObj = jsonObj.recentorder;	
    // Recent Order Item box target
    var ulObj = $("#recentorder-list-box_"+id);

    // Display error message on top
    if (jsonObj.messages.error.length >0) {
	
        var pdObj = $("#productError_"+id);
        var tplHTML = $("#ErrorPageTemplate").html();	
        for (key in jsonObj.messages) {
            var re = new RegExp("%"+key+"%", "g");
            tplHTML = tplHTML.replace(re,jsonObj.messages[key]);
        }
        pdObj.append(tplHTML);			
        $("#productError_"+id).css("display","block");		
    }


    // have recent order
    if ( dataObj && dataObj.length > 0 ) {	
								
        for ( var i=0; i<dataObj.length; i++ ) {			
            var tplHTML = $("#RecentOrderTemplate").html();			
            // date
            var re = new RegExp("%created_at%", "g");
            tplHTML = tplHTML.replace(re,dataObj[i].order.created_at);							
            // status
            var re = new RegExp("%status%", "g");
            tplHTML = tplHTML.replace(re,dataObj[i].status);	
								
            // grand_total
            var curr = dataObj[i].currency_code;
            var formatprice = formatCurrency($.formatCurrency.regions[curr], dataObj[i].order.total.grand_total);
            var re = new RegExp("%grand_total%", "g");
            tplHTML = tplHTML.replace(re,formatprice);					
			
								
            // update id
            var re3 = new RegExp("%id%", "g");
            tplHTML = tplHTML.replace(re3,dataObj[i].order.id);	
			
            // update rid
            var re4 = new RegExp("%rid%", "g");
            tplHTML = tplHTML.replace(re4,id);								
            ulObj.append(tplHTML);	
			
            // Recent Ordered Item box
            var orderObj = $("#order-list-attr-"+id+dataObj[i].order.id);
            //console.log(orderObj);
			
            if ( dataObj[i].order.items && dataObj[i].order.items.length > 0 ) {	
				
                var itemsObj = dataObj[i].order.items;		
                for ( var m=0; m<itemsObj.length; m++ ) {
                    var tplHTML = $("#RecentOrderItemTemplate").html();			
					
                    for (key in itemsObj[m]) {
                        //console.log(key);
                        if(key == "price") {
                            var formatprice = formatCurrency($.formatCurrency.regions[curr], itemsObj[m][key]);
                            var re = new RegExp("%fmtprice%", "g");
                            tplHTML = tplHTML.replace(re,formatprice);
                        }
                        var re = new RegExp("%"+key+"%", "g");
                        tplHTML = tplHTML.replace(re,itemsObj[m][key]);				
                    }
                    //console.log(tplHTML);
                    orderObj.append(tplHTML);
                }
				
            } //if
																
        }	// for 	
    }
    else {
        // empty order message
        $("#cart-empty_"+id).css("display","block");	
	
    }
	
    // stop loading
    $(".ui-loader").css("display","none");
}

// OrderInfo Page JSON Functions
function renderOrderInfoPage(jsonObj,id) {

    var dataObj = jsonObj.checkout_success.order;
	
    // Confirmation Merchant Info box
    var MercObj = $("#merchant-box_"+id);
    var MerctplHTML = $("#ConfirmMerchantTemplate").html();		
	
    // Store info
    var storeObj = jsonObj.checkout_success.store;
    for (key in storeObj) {	
        if(key == "img") {
            var reimg = new RegExp("%"+key+"%", "g");
            MerctplHTML = MerctplHTML.replace(reimg,'<img src="'+storeObj[key]+'">');
        } else {
            var re1 = new RegExp("%"+key+"%", "g");
            MerctplHTML = MerctplHTML.replace(re1,storeObj[key]);
        }
    }

    // Pickup-time
    var pctime = jsonObj.checkout_success.pickup_time;
    var re2 = new RegExp("%pickup_time%", "g");
    MerctplHTML = MerctplHTML.replace(re2,pctime);	
		
    // QR box
    var QRObj = $("#confirm-qr-box_"+id);
    var QRtplHTML = $("#ConfirmQRTemplate").html();		
	
    // id and buyer name
    var transcid = jsonObj.checkout_success.order.id;
    var buyernm = jsonObj.checkout_success.buyername;
    var gtotal = jsonObj.checkout_success.order.total.grand_total;
    var qrimg = jsonObj.qrcode.url;

    var shiptitle = jsonObj.checkout_success.shipping_title;
    var curr = jsonObj.checkout_success.currency_code;
	
    var re3 = new RegExp("%id%", "g");
    QRtplHTML = QRtplHTML.replace(re3,transcid);
    MerctplHTML = MerctplHTML.replace(re3,transcid);
    MercObj.append(MerctplHTML);
    var re4 = new RegExp("%buyername%", "g");
    QRtplHTML = QRtplHTML.replace(re4,buyernm);

    var formatprice = formatCurrency($.formatCurrency.regions[curr], gtotal);
    var re5 = new RegExp("%grand_total%", "g");
    QRtplHTML = QRtplHTML.replace(re5,formatprice);	

    if(qrimg) {
        var re6 = new RegExp("%qrcode%", "g");
        QRtplHTML = QRtplHTML.replace(re6,'<img src="'+qrimg+'">');		
        var re7 = new RegExp("%instruction%", "g");
        QRtplHTML = QRtplHTML.replace(re7,jsonObj.qrcode.text);
    }else {
        var re6 = new RegExp("%qrcode%", "g");
        QRtplHTML = QRtplHTML.replace(re6,'');			
        var re7 = new RegExp("%instruction%", "g");
        QRtplHTML = QRtplHTML.replace(re7,'');			
    }
					
    QRObj.append(QRtplHTML);	
	
	
    // Ordered Item box
    var ulObj = $("#order-list-box_"+id);
    var tplHTML = $("#OrderedInfoTemplate").html();	
    var re7 = new RegExp("%rid%", "g");
    tplHTML = tplHTML.replace(re7,id);
    ulObj.append(tplHTML);
	
	
    // render Order item 
    var OrderItemObj = $("#order-list-container-"+id);
			
    // Ordered Item 
    if ( dataObj.items && dataObj.items.length > 0 ) {	
        var itemsObj = dataObj.items;
		
        for ( var i=0; i<itemsObj.length; i++ ) {
            var tplHTML = $("#OrderedInfoItemTemplate").html();			
            var tplOptStr ='';
			
            for (key in itemsObj[i]) {
                if(key == "options" && itemsObj[i].options.length > 0){
				
                    var optObj = itemsObj[i].options;
                    for ( var u=0; u<optObj.length; u++ ) {
                        var tplOptHTML = "<b>%label%</b>: %value%<br>";
                        for (key in optObj[u]) {
                            var re = new RegExp("%"+key+"%", "g");
                            tplOptHTML = tplOptHTML.replace(re,optObj[u][key]);														
                        }
                        tplOptStr += tplOptHTML;
                    //alert(tplOptStr);
                    }					
                    var re = new RegExp("%optionstr%", "g");
                    tplHTML = tplHTML.replace(re,tplOptStr);
					
                } else {
                    if(key == 'price') {
                        var formatprice = formatCurrency($.formatCurrency.regions[curr], itemsObj[i].price);
                        var re = new RegExp("%fmtprice%", "g");
                        tplHTML = tplHTML.replace(re,formatprice);
                    }
                    var re = new RegExp("%"+key+"%", "g");
                    tplHTML = tplHTML.replace(re,itemsObj[i][key]);
                }
            }
						
            var re2 = new RegExp("%optionstr%", "g");
            tplHTML = tplHTML.replace(re2,'');						
            OrderItemObj.append(tplHTML);
        }
    }
	
				
    // Confirmation total box
    var cart2Obj = $("#cart-total-box_"+id);
    var tplHTML = $("#ConfirmTotalTemplate").html();	
    totalObj = dataObj.total;
    for (key in totalObj) {
        var formatprice = formatCurrency($.formatCurrency.regions[curr], totalObj[key]);
        var re = new RegExp("%"+key+"%", "g");
        tplHTML = tplHTML.replace(re,formatprice);
    }

    var re2 = new RegExp("%shipping_title%", "g");
    tplHTML = tplHTML.replace(re2,shiptitle);
    cart2Obj.append(tplHTML);	
	
    // display button
    $("#contshop_"+id).css("display","block");
    // stop loading
    $(".ui-loader").css("display","none");
}

// Confirmation Page JSON Functions
function renderConfirmPage(jsonObj,id) {
	
    var dataObj = jsonObj.checkout_success.order;
	
    // Confirmation Merchant Info box
    var MercObj = $("#merchant-box_"+id);
    var MerctplHTML = $("#ConfirmMerchantTemplate").html();		

    // Store info
    var storeObj = jsonObj.checkout_success.store;
    for (key in storeObj) {	
        if(key == "img") {
            var reimg = new RegExp("%"+key+"%", "g");
            MerctplHTML = MerctplHTML.replace(reimg,'<img src="'+storeObj[key]+'">');
        } else {
            var re1 = new RegExp("%"+key+"%", "g");
            MerctplHTML = MerctplHTML.replace(re1,storeObj[key]);
        }
    }

    // Pickup-time
    var pctime = jsonObj.checkout_success.pickup_time;
    var re2 = new RegExp("%pickup_time%", "g");
    MerctplHTML = MerctplHTML.replace(re2,pctime);
		
    // Confirmation QR box
    var QRObj = $("#confirm-qr-box_"+id);
    var QRtplHTML = $("#ConfirmQRTemplate").html();		
	
    // id and buyer name
    var transcid = jsonObj.checkout_success.order.id;
    var buyernm = jsonObj.checkout_success.buyername;
    var gtotal = jsonObj.checkout_success.order.total.grand_total;
    var qrimg = jsonObj.qrcode.url;
	
    var shiptitle = jsonObj.checkout_success.shipping_title;
    var curr = jsonObj.checkout_success.currency_code;

    var re3 = new RegExp("%id%", "g");
    QRtplHTML = QRtplHTML.replace(re3,transcid);
    MerctplHTML = MerctplHTML.replace(re3,transcid);
    MercObj.append(MerctplHTML);
    var re4 = new RegExp("%buyername%", "g");
    QRtplHTML = QRtplHTML.replace(re4,buyernm);
	
    var formatprice = formatCurrency($.formatCurrency.regions[curr], gtotal);
    var re5 = new RegExp("%grand_total%", "g");
    QRtplHTML = QRtplHTML.replace(re5,formatprice);		
	
    if(qrimg) {
        var re6 = new RegExp("%qrcode%", "g");
        QRtplHTML = QRtplHTML.replace(re6,'<img src="'+qrimg+'">');		
        var re7 = new RegExp("%instruction%", "g");
        QRtplHTML = QRtplHTML.replace(re7,jsonObj.qrcode.text);
    }else {
        var re6 = new RegExp("%qrcode%", "g");
        QRtplHTML = QRtplHTML.replace(re6,'');			
        var re7 = new RegExp("%instruction%", "g");
        QRtplHTML = QRtplHTML.replace(re7,'');			
    }			
    QRObj.append(QRtplHTML);
	
	
    // Confirmation Ordered Item box
    var ulObj = $("#cart-list-box_"+id);
				
    if ( dataObj.items && dataObj.items.length > 0 ) {	
        var itemsObj = dataObj.items;
		
        for ( var i=0; i<itemsObj.length; i++ ) {
            var tplHTML = $("#ConfirmOrderedItemTemplate").html();			
            var tplOptStr ='';
			
            for (key in itemsObj[i]) {
                if(key == "options" && itemsObj[i].options.length > 0){
				
                    var optObj = itemsObj[i].options;
                    for ( var u=0; u<optObj.length; u++ ) {
                        var tplOptHTML = "<b>%label%</b>: %value%<br>";
                        for (key in optObj[u]) {
                            var re = new RegExp("%"+key+"%", "g");
                            tplOptHTML = tplOptHTML.replace(re,optObj[u][key]);														
                        }
                        tplOptStr += tplOptHTML;
                    }					
                    var re = new RegExp("%optionstr%", "g");
                    tplHTML = tplHTML.replace(re,tplOptStr);
					
                } else {
                    if(key == 'price') {
                        var formatprice = formatCurrency($.formatCurrency.regions[curr], itemsObj[i].price);
                        var re = new RegExp("%fmtprice%", "g");
                        tplHTML = tplHTML.replace(re,formatprice);
                    }
                    var re = new RegExp("%"+key+"%", "g");
                    tplHTML = tplHTML.replace(re,itemsObj[i][key]);
                }
            }
						
            var re2 = new RegExp("%optionstr%", "g");
            tplHTML = tplHTML.replace(re2,'');
            ulObj.append(tplHTML);
        }
    }

						
    // Confirmation total box
    var cart2Obj = $("#cart-total-box_"+id);
    var tplHTML = $("#ConfirmTotalTemplate").html();	
    totalObj = dataObj.total;
    for (key in totalObj) {
        var formatprice = formatCurrency($.formatCurrency.regions[curr], totalObj[key]);
        var re = new RegExp("%"+key+"%", "g");
        tplHTML = tplHTML.replace(re,formatprice);
    }

    var re2 = new RegExp("%shipping_title%", "g");
    tplHTML = tplHTML.replace(re2,shiptitle);
    cart2Obj.append(tplHTML);	
	
    // display button
    $("#contshop_"+id).css("display","block");
    // stop loading
    $(".ui-loader").css("display","none");	
}


// Cart Page JSON Functions
function renderCartPage(jsonObj,id) {

    var dataObj = jsonObj.cart_index;	
	
    // Cart Ordered Item box target
    var ulObj = $("#cart-list-box_"+id);

    // Display error message on top
    if (jsonObj.messages.error.length >0) {
	
        var pdObj = $("#productError_"+id);
        var tplHTML = $("#ErrorPageTemplate").html();	
        for (key in jsonObj.messages) {
            var re = new RegExp("%"+key+"%", "g");
            tplHTML = tplHTML.replace(re,jsonObj.messages[key]);
        }
        pdObj.append(tplHTML);	
		
        $("#productError_"+id).css("display","block");		
    }

    var curr = dataObj.currency_code;		
				
    // have cart item
    if ( dataObj.items && dataObj.items.length > 0 ) {	
		
        var itemsObj = dataObj.items;	
        for ( var i=0; i<itemsObj.length; i++ ) {
			
            var tplHTML = $("#CartOrderedItemTemplate").html();			
            var tplOptStr ='';
			
            for (key in itemsObj[i]) {
                if(key == "options" && itemsObj[i].options.length > 0){
				
                    var optObj = itemsObj[i].options;
                    for ( var u=0; u<optObj.length; u++ ) {
                        var tplOptHTML = "<b>%label%</b>: %value%<br>";
                        for (key in optObj[u]) {		
                            var re = new RegExp("%"+key+"%", "g");
                            tplOptHTML = tplOptHTML.replace(re,optObj[u][key]);														
                        }
                        tplOptStr += tplOptHTML;
                    //alert(tplOptStr);
                    }					
                    var re = new RegExp("%optionstr%", "g");
                    tplHTML = tplHTML.replace(re,tplOptStr);
					
                } else {
                    if(key == 'price') {
                        var formatprice = formatCurrency($.formatCurrency.regions[curr], itemsObj[i].price);
                        var re = new RegExp("%fmtprice%", "g");
                        tplHTML = tplHTML.replace(re,formatprice);
                    }
                    var re = new RegExp("%"+key+"%", "g");
                    tplHTML = tplHTML.replace(re,itemsObj[i][key]);	
									
                }
            }
			
            // some product not have options
            var re2 = new RegExp("%optionstr%", "g");
            tplHTML = tplHTML.replace(re2,'');	
            // update rid
            var re3 = new RegExp("%rid%", "g");
            tplHTML = tplHTML.replace(re3,id);											
            ulObj.append(tplHTML);	
				
				
            // render cart item Quantity
            var qtyObj = $("#qty_"+id+"_"+itemsObj[i].id);
            maxno = 10;
            if(itemsObj[i].maxqty < 10)
                maxno = itemsObj[i].maxqty;
				
            if(itemsObj[i].quantity > 10) 
                maxno =	itemsObj[i].quantity;
							
            for ( var s=1; s<=maxno; s++ ) {
                var tpliHTML = $("#CartSelectItemTemplate").html();
                var re = new RegExp("%option_id%", "g");
                tpliHTML = tpliHTML.replace(re,s);
				
                var re = new RegExp("%selected%", "g");
                if(itemsObj[i].quantity == s) 
                    tpliHTML = tpliHTML.replace(re,"selected");
                else
                    tpliHTML = tpliHTML.replace(re,"");
				
                qtyObj.append(tpliHTML);
				
                // update the select text 
                CartQtySelectUpdate("qty_"+id+"_"+itemsObj[i].id,id);
            }				

        }

        // display cart Total box
        var cart2Obj = $("#cart-total-box_"+id);
        var TotaltplHTML = $("#CartTotalTemplate").html();	
        totalObj = dataObj.total;
        for (key in totalObj) {
            var formatprice = formatCurrency($.formatCurrency.regions[curr], totalObj[key]);
            var re = new RegExp("%"+key+"%", "g");
            TotaltplHTML = TotaltplHTML.replace(re,formatprice);		
        }
        var re2 = new RegExp("%shipping_title%", "g");
        TotaltplHTML = TotaltplHTML.replace(re2,jsonObj.cart_index.shipping_title);
        cart2Obj.append(TotaltplHTML);
	

        	
        // Phase 2 - Cart Pickup Time box                 
        if(jsonObj.cart_index.open_datetime != null){
            if(parseInt(jsonObj.cart_index.open_datetime.enabled)){
                var cart3Obj = $("#cart-pickuptime-box_"+id);
                var tplHTML = $("#CartPickupTemplate").html();			
                pctimeObj = dataObj.pickup_time;

                var re = new RegExp("%sid%", "g");
                tplHTML = tplHTML.replace(re,id);

                var re = new RegExp("%currenttime%", "g");
                tplHTML = tplHTML.replace(re,pctimeObj);
                cart3Obj.append(tplHTML);

                //$("#cart-pickuptime-box_"+id).css("display","block");
                //Set pickup time box
                renderPickupTime(jsonObj.cart_index.open_datetime,id);
            }
        }
        
        // Display Static Block
        var cart4Obj = $("#cart-staticblock-box_"+id);
        var tplHTML = $("#CartStaticBlockTemplate").html();			
        pctimeObj = dataObj.static_block;
        var re5 = new RegExp("%static_block%", "g");
        tplHTML = tplHTML.replace(re5,pctimeObj);
        cart4Obj.append(tplHTML);	
        if(pctimeObj.length>0)
            $("#cart-staticblock-box_"+id).css("display","block");
							
        /*		// Phase 2	- display discount value	
		if(totalObj["discount"] > 0) {
			$("#cart-discount").css("display","block");
		}
*/			
        // display pay now button	
        $("#cart-update_"+id).css("display","block");
		
        var btnObj = $("#pay-now_"+id);
        var btnHTML = $("#CartPayNowTemplate").html();
        /*if(dataObj.paypal_checkin) {
			var re = new RegExp("%function%", "g");
			btnHTML = btnHTML.replace(re,"$('.ui-loader').css('display','block'); $('#payform_"+id+"').submit();");				
		
		} else {*/	
        var re = new RegExp("%function%", "g");
        //btnHTML = btnHTML.replace(re,"alert('Unable to pay.')");
        btnHTML = btnHTML.replace(re,"validate_pickuptime_and_pay(" + id + ");");
        //}
        btnObj.append(btnHTML);
        $("#pay-now_"+id).css("display","block");
		
	
    }
    else {
        // empty cart message
        $("#cart-empty_"+id).css("display","block");	
	
    }
    // stop loading
    $(".ui-loader").css("display","none");	
}

// Cart Page onChange Quantity select list box -- update text 
function CartQtySelectUpdate(id,sid) {
    var selectText = $('#'+id+'>option:selected').text();
    $('#'+id+'_text').html(selectText);
	
}


// Navigation JSON Functions
function renderNavigation(jsonObj) {
    var dataObj = jsonObj.catalog_navigation;
    if ( dataObj.items && dataObj.items.length > 0 ) {
        var newulObj = $("#accordionContainer");		
        renderNavigationItems(newulObj, dataObj.items, dataObj.url);
    }
	
    $('.accordion').accordion();
}

function renderNavigationItems(ulObj, itemsObj, url) {

    for ( var i=0; i<itemsObj.length; i++ ) {
        var tplHTML = $("#navListTemplate").html();
		
        var liObj = $("<li/>");
        ulObj.append(liObj);

        if ( itemsObj[i].items && itemsObj[i].items.length > 0 ) {
            var re = new RegExp("%url%", "g");
            tplHTML = tplHTML.replace(re,"#");
			
            for (key in itemsObj[i]) {
                var re = new RegExp("%"+key+"%", "g");
                tplHTML = tplHTML.replace(re,itemsObj[i][key]);
            }
            liObj.append(tplHTML);
			
            liObj.addClass("expandable");
            var newulObj = $("<ul/>");
            liObj.append(newulObj);
            renderNavigationItems(newulObj, itemsObj[i].items, url);
			
        } else {
		
            var link = "#";
            var re = new RegExp("%url%", "g");
            if ( itemsObj[i].id ) {
                //link = "javascript:navigationURL('"+url+"?id="+itemsObj[i].id+"');"; // test mode
                link = "javascript:navigationURL('"+url+"/id/"+itemsObj[i].id+"');";
            }
            tplHTML = tplHTML.replace(re,link);
			
            for (key in itemsObj[i]) {
                var re = new RegExp("%"+key+"%", "g");
                tplHTML = tplHTML.replace(re,itemsObj[i][key]);
            }
            liObj.append(tplHTML);
        }
    }
}

// Product List JSON Functions
function renderProductList(jsonObj,id) {
    var dataObj = jsonObj.product_list;
    var ulObj = $("#productList_"+id);
	
    var tplHTML = $("#productListTitleTemplate").html();
    var re = new RegExp("%category%", "g");
    tplHTML = tplHTML.replace(re,jsonObj.product_list.category);
    ulObj.append(tplHTML);
    
    if ( dataObj.items && dataObj.items.length > 0 ) {
        var itemsObj = dataObj.items;
        for ( var i=0; i<itemsObj.length; i++ ) {
            var imageFound = 0;
            var tplHTML = $("#productListTemplate").html();
            for (key in itemsObj[i]) {
                if ( key == "image" ) {
                    var re = new RegExp("%"+key+"%", "g");
                    if(itemsObj[i][key]) {
                        tplHTML = tplHTML.replace(re,'<img src="'+itemsObj[i][key]+'">');
                        imageFound++;
                    }
                } else {
                    var re = new RegExp("%"+key+"%", "g");
                    tplHTML = tplHTML.replace(re,itemsObj[i][key]);
                }
            }
			
            if ( imageFound < 1 ) {
                var re = new RegExp("%image%", "g");
                tplHTML = tplHTML.replace(re,'<img src="css/images/img_holder_generic.png">');
            }
            ulObj.append(tplHTML);
        }
    }
    // stop loading
    $(".ui-loader").css("display","none");	
}

// Product Detail JSON Functions
function renderProductInfo(jsonObj,id) {

    var dataObj = jsonObj.product_info;		
    var pdObj = $("#productDetail_"+id);	
	
    if ( dataObj.details.id ) {
	
        // product info
        var tplHTML = $("#productDetailTemplate").html();
        for (key in dataObj.details) {
            var re = new RegExp("%"+key+"%", "g");
            tplHTML = tplHTML.replace(re,dataObj.details[key]);
        }
		
        // product favorite 
        if( jsonObj.favorite.status == 1 ) {
            var ref1 = new RegExp("%favtext%", "g");
            tplHTML = tplHTML.replace(ref1,"Saved");
            var ref2 = new RegExp("%savedfav%", "g");
            tplHTML = tplHTML.replace(ref2,"saved-fav");						
						
        } else {
            var ref1 = new RegExp("%favtext%", "g");
            tplHTML = tplHTML.replace(ref1,"Save to Favorites");	
            var ref2 = new RegExp("%savedfav%", "g");
            tplHTML = tplHTML.replace(ref2,"");												
        }
				
        var re = new RegExp("%sid%", "g");
        tplHTML = tplHTML.replace(re,id);
		
        var curr = dataObj.details.currency_code;		
        var formatdefprice = formatCurrency($.formatCurrency.regions[curr], dataObj.details.price);
        var formatspeprice = formatCurrency($.formatCurrency.regions[curr], dataObj.details.specialprice);
        var formatfnlprice = formatCurrency($.formatCurrency.regions[curr], dataObj.details.finalprice);
		
        var re = new RegExp("%fmtprice%", "g");
        tplHTML = tplHTML.replace(re,formatdefprice);
        var re = new RegExp("%fmtspecialprice%", "g");
        tplHTML = tplHTML.replace(re,formatspeprice);
        var re = new RegExp("%fmtfinalprice%", "g");
        tplHTML = tplHTML.replace(re,formatfnlprice);	
		
        pdObj.append(tplHTML);

        // enable favorite
        if(jsonObj.favorite.enable ==1) {
            $("#favorite_"+id).css("display","block");
        }

        //var config = dataObj.details.options.configurable;
        //console.log(config);
        //
        // product options
        //if ( dataObj.details.options && dataObj.details.options.length > 0 ) {
        renderProductOptions(dataObj.details.options, id, curr);
        //}
		
        // product image
        if ( dataObj.media && dataObj.media.length > 0 ) {
            if (dataObj.media[0].url) {
                $("#productImage_"+id).attr("src",dataObj.media[0].url);
            } 
        }
					
        // Product price
        var ppObj = $("#productPrice_"+id);
        var tplpHTML = $("#productTotalPriceTemplate").html();
        for (key in dataObj.details) {
            var re = new RegExp("%"+key+"%", "g");
            tplpHTML = tplpHTML.replace(re,dataObj.details[key]);
        }
		
        if( dataObj.details['price'] > 0 ) {
            $("#finalpricebox").css("display","block");
        }

			
        // display button
        $("#addtocart_"+id).css("display","block");	

        var re = new RegExp("%sid%", "g");
        tplpHTML = tplpHTML.replace(re,id);	
		
        ppObj.append(tplpHTML);	
		
        // render qty
        var qtyObj = $("#qty_"+id);

        maxno = 10;
        if(dataObj.details.maxqty < 10)
            maxno = dataObj.details.maxqty;
			
        for ( var i=1; i<=maxno; i++ ) {
            var tpliHTML = $("#productOptionSelectItemTemplate").html();
            var re = new RegExp("%option_id%", "g");
            tpliHTML = tpliHTML.replace(re,i);
            var re = new RegExp("%title%", "g");
            tpliHTML = tpliHTML.replace(re,i);
            var re = new RegExp("%price%", "g");
            tpliHTML = tpliHTML.replace(re,"0");
            var re = new RegExp("%optprice%", "g");
            tpliHTML = tpliHTML.replace(re,"");
            qtyObj.append(tpliHTML);
        }
					
    } // if have product detail items
	
	
    // Display error message in product page
    if (jsonObj.messages.error.length >0) {
	
        var pdObj = $("#productError_"+id);
        var tplHTML = $("#ErrorPageTemplate").html();	
        for (key in jsonObj.messages) {
            var re = new RegExp("%"+key+"%", "g");
            tplHTML = tplHTML.replace(re,jsonObj.messages[key]);
        }
        var re = new RegExp("%sid%", "g");
        tplHTML = tplHTML.replace(re,id);		
        pdObj.append(tplHTML);	
		
        //var jsHTML = '<script>$("div").filter(\'#errorchange%sid%\').fadeOut(4000);</script>';
        //jsHTML = jsHTML.replace(re,id);
        //pdObj.append(jsHTML);
		
        $("#productError_"+id).css("display","block");		
    }	
		
    // update amount
    updateProductTotal(id, curr);
	
    // stop loading
    $(".ui-loader").css("display","none");

}

// product option type - radio / select list / checkbox
function renderProductOptions(jsonObj, id, curr) {
    for ( var i=0; i<jsonObj.length; i++ ) {
        if ( jsonObj[i].type == "drop_down" ) {
            // Downdown options
            renderProductSelect(jsonObj[i], id, jsonObj[i].is_require, curr);
			
        } else if ( jsonObj[i].type == "radio" ) {
            // Radio options - select first option if is required
            renderProductRadio(jsonObj[i], id, jsonObj[i].is_require, curr);

        } else if ( jsonObj[i].type == "checkbox" ) {
            // Radio options - select first option if is required
            renderProductCheckbox(jsonObj[i], id, jsonObj[i].is_require, curr);			
        }
    }
}

// product option - select list type
function renderProductSelect(jsonObj, id, isreq, curr) {

    var poObj = $("#productOption_"+id);
    var tplHTML = $("#productOptionSelectTemplate").html();
    for (key in jsonObj) {
        var re = new RegExp("%"+key+"%", "g");
        tplHTML = tplHTML.replace(re,jsonObj[key]);
    }
	
    var re = new RegExp("%sid%", "g");
    tplHTML = tplHTML.replace(re,id);
    var re = new RegExp("%currency_code%", "g");
    tplHTML = tplHTML.replace(re,curr);

    if(jsonObj.configurable) {
        var re = new RegExp("%selectname%", "g");
        tplHTML = tplHTML.replace(re,"super_attribute");	
    } else {
        var re = new RegExp("%selectname%", "g");
        tplHTML = tplHTML.replace(re,"options");	
    }		
				
    poObj.append(tplHTML);
	
	
    if ( jsonObj.options && jsonObj.options.length > 0 ) {
        var poiObj = $("#opt_"+id+"_"+jsonObj.option_id);
		
        if(isreq==0) {
            optHTML = "<option value='' price='0'>Please select (optional)</option>";		
            poiObj.append(optHTML);
		
        }/*else {
			optHTML = "<option value='' price='0'>Please select "+jsonObj.title+"</option>";		
			poiObj.append(optHTML);
		}*/
			
        for ( var i=0; i<jsonObj.options.length; i++ ) {
            var tpliHTML = $("#productOptionSelectItemTemplate").html();
									
            for (key in jsonObj.options[i]) {					
                if(jsonObj.options[i].price >0) {
                    var formatprice = formatCurrency($.formatCurrency.regions[curr], jsonObj.options[i]['price']);
                    var re = new RegExp("%optprice%", "g");
                    tpliHTML = tpliHTML.replace(re,'('+formatprice+')');
                } else {
                    var re = new RegExp("%optprice%", "g");
                    tpliHTML = tpliHTML.replace(re,'');				
                }
				
                if(isreq==1 && i==0) {
                    var re = new RegExp("%selected%", "g");
                    tpliHTML = tpliHTML.replace(re,'selected');				
                } else {
                    var re = new RegExp("%selected%", "g");
                    tpliHTML = tpliHTML.replace(re,'');	
                }
				
                var re = new RegExp("%"+key+"%", "g");
                tpliHTML = tpliHTML.replace(re,jsonObj.options[i][key]);
            }
            poiObj.append(tpliHTML);
        }
    }
	
    // update the select text 
    productOptionSelectUpdate("opt_"+id+"_"+jsonObj.option_id, id, curr );
}

// onChange select list box -- update text and amount 
function productOptionSelectUpdate(id,sid, curr) {
    var selectText = $('#'+id+'>option:selected').text();
    $('#'+id+'_text').html(selectText);
	
    // update amount
    updateProductTotal(sid, curr);
}

// product option - radio type in tab 
function renderProductRadio(jsonObj, id, isreq, curr) {
    //if ( jsonObj.options.length == 1 ) {
    // alert("1 option");	
    //} else if ( jsonObj.options.length > 1 && jsonObj.options.length < 4 ) {
	
    if( jsonObj.options.length < 4 ) {
        var count = 0;
        // get the number of radio options - to use different css
        var radioType = jsonObj.options.length;
        // get target div
        var poObj = $("#productOption_"+id);
        // get template content
        var tplHTML = $("#productOptionRadioTemplate").html();
        for (key in jsonObj) {
            var re = new RegExp("%"+key+"%", "g");
            tplHTML = tplHTML.replace(re,jsonObj[key]);
        }
		
        var re2 = new RegExp("%display%", "g");
        // hide if only one option
        /*if ( jsonObj.options.length == 1 && isreq) 	
			tplHTML = tplHTML.replace(re2,'display:none');
		else*/
        tplHTML = tplHTML.replace(re2,'display:block');
		
        var re3 = new RegExp("%sid%", "g");
        tplHTML = tplHTML.replace(re3,id);
        poObj.append(tplHTML);
		
        //var onlyone=0;
        //if(jsonObj.options.length == 1) onlyone =1;
		
        if ( jsonObj.options && jsonObj.options.length > 0 ) {
            var poiObj = $("#div_"+id+"_"+jsonObj.option_id);
            for ( var i=0; i<jsonObj.options.length; i++ ) {
                var tpliHTML = $("#productOptionRadioItemTemplate").html();
                //if(onlyone)
                //tpliHTML = $("#productOptionRadioHiddenTemplate").html();
					
                for (key in jsonObj.options[i]) {
					
                    if(key == 'price') {
                        var formatprice = formatCurrency($.formatCurrency.regions[curr], jsonObj.options[i]['price']);
                        var re = new RegExp("%fmtprice%", "g");
                        tpliHTML = tpliHTML.replace(re,formatprice);
                    }	
					
                    var re = new RegExp("%"+key+"%", "g");
                    tpliHTML = tpliHTML.replace(re,jsonObj.options[i][key]);
					
                }
                // if is required, select the first option and checked
                if(isreq==1 && i==0) {
                    var re = new RegExp("%ui-radio%", "g");
                    tpliHTML = tpliHTML.replace(re,"ui-radio-on");
                    var re = new RegExp("%radiochecked%", "g");
                    tpliHTML = tpliHTML.replace(re,'checked');
                } else {
                    var re = new RegExp("%radiochecked%", "g");
                    tpliHTML = tpliHTML.replace(re,'');
                }	
                var re = new RegExp("%parent_option_id%", "g");
                tpliHTML = tpliHTML.replace(re,jsonObj.option_id);
                var re = new RegExp("%sid%", "g");
                tpliHTML = tpliHTML.replace(re,id);
                var re = new RegExp("%currency_code%", "g");
                tpliHTML = tpliHTML.replace(re,curr);
                var re = new RegExp("%count%", "g");
                tpliHTML = tpliHTML.replace(re,i);
                var re = new RegExp("%radiotype%", "g");
                tpliHTML = tpliHTML.replace(re,radioType);		
								
									
                //console.log(isreq);
                //if(onlyone && isreq==1)
                //poObj.append(tpliHTML);
                //else if(!onlyone) 
                poiObj.append(tpliHTML);
					
                count++;
            }
        }
        $("#count_"+id+"_"+jsonObj.option_id).html(count);
		
		
    } else {
        // if radio options >= 4, display as list type
        renderProductSelect(jsonObj, id, isreq, curr);
    }
}

// onclick radio button - update css
function updateProductRadio(id,option_id,count) {
    var num = $('#count_'+id+'_'+option_id).html();
	
    for ( var i=0; i<num; i++ ) {
        var rObj = $('#label_'+id+'_'+option_id+'_'+i);
        if ( count == i ) {
            rObj.attr("class","ui-btn ui-radio-on ui-corner-left ui-btn-up-c");
        } else {
            rObj.attr("class","ui-btn ui-radio-off ui-corner-left ui-btn-up-c");
        }
    }
}

// product option - Checkbox 
function renderProductCheckbox(jsonObj, id, isreq, curr) {
    //if ( jsonObj.options.length == 1 ) {
    // alert("1 option");		
    //} else {
    var count = 0;
    // get the number of radio options - to use different css
    var radioType = jsonObj.options.length;
    // get target div
    var poObj = $("#productOption_"+id);
    // get template content
    var tplHTML = $("#productOptionCheckboxTemplate").html();
    for (key in jsonObj) {
        var re = new RegExp("%"+key+"%", "g");
        tplHTML = tplHTML.replace(re,jsonObj[key]);
    }
    var re = new RegExp("%sid%", "g");
    tplHTML = tplHTML.replace(re,id);
    poObj.append(tplHTML);

    if ( jsonObj.options && jsonObj.options.length > 0 ) {
        var poiObj = $("#div_"+id+"_"+jsonObj.option_id);
			
        for ( var i=0; i<jsonObj.options.length; i++ ) {
            var tpliHTML = $("#productOptionCheckboxItemTemplate").html();
				
            for (key in jsonObj.options[i]) {				
					
                var formatprice = formatCurrency($.formatCurrency.regions[curr], jsonObj.options[i]['price']);
                var re = new RegExp("%fmtprice%", "g");
                tpliHTML = tpliHTML.replace(re,formatprice);
										
                var re = new RegExp("%"+key+"%", "g");
                tpliHTML = tpliHTML.replace(re,jsonObj.options[i][key]);
					
            }
								
            var re = new RegExp("%parent_option_id%", "g");
            tpliHTML = tpliHTML.replace(re,jsonObj.option_id);
            var re = new RegExp("%sid%", "g");
            tpliHTML = tpliHTML.replace(re,id);
            var re = new RegExp("%currency_code%", "g");
            tpliHTML = tpliHTML.replace(re,curr);
            var re = new RegExp("%count%", "g");
            tpliHTML = tpliHTML.replace(re,i);
								
            poiObj.append(tpliHTML);
            count++;
        }
    }
    $("#count_"+id+"_"+jsonObj.option_id).html(count);	
//}
}

// onclick checkbox - delay read
function updateProductCheckbox(id,parent_id,option_id) {
    setTimeout("setProductCheckbox('"+id+"','"+parent_id+"','"+option_id+"')",100);	
}

// onclick checkbox - update css
function setProductCheckbox(id,parent_id,option_id) {
    var cObj = $('#label_'+id+'_'+parent_id+'_'+option_id);
    var cInput = $('#opt_'+id+'_'+parent_id+'_'+option_id);
    if ( cInput.is(':checked') ) {
        cObj.attr("class","ui-btn ui-checkbox-on ui-corner-left ui-btn-up-c");
    } else {
        cObj.attr("class","ui-btn ui-checkbox-off ui-corner-left ui-btn-up-c");
    }
}

// onmouseup radio button - update amount
function updateProductTotal(id, curr) {
    setTimeout("updatePriceTotal('"+id+"','"+curr+"')",500);
}

// update amount in product page
function updatePriceTotal(id,curr) {
    var price_def = 0;
    var price_fnl = 0;
	
    // default price
    var defprice = $('#defprice_'+id).html();	
    var specialprice = $('#specialprice_'+id).html();
    var finalprice = $('#finalprice_'+id).html();
		
    price_def += Number(defprice);
    price_fnl += Number(finalprice);	
		
    // radio type options
    $('#frm_'+id).find(':radio').each(function(i){
        if ( $(this).attr("checked") ) {
            price_def += Number($(this).attr('price'));
            price_fnl += Number($(this).attr('price'));
        }
    });
	
    // checkbox type options
    $('#frm_'+id).find(':checkbox').each(function(i){
        if ( $(this).attr("checked") ) {
            price_def += Number($(this).attr('price'));
            price_fnl += Number($(this).attr('price'));
        }
    });
	
    // select type options
    $('#frm_'+id).find(':selected').each(function(i){
        price_def += Number($(this).attr('price'));
        price_fnl += Number($(this).attr('price'));
    });
	
    // Total Amount
    var qty = Number($('#qty_'+id).val());
    total_def = qty * price_def;
    total_fnl = qty * price_fnl;
    // Final price on top
    var potObj = $("#productTotalFinalPriceTop_"+id);
    var formattopprice = formatCurrency($.formatCurrency.regions[curr], total_fnl.toFixed(2));
    potObj.html(formattopprice);
	
    // price at bottom	
    var poObj = $("#productTotalPrice_"+id);		
    var formatbtmprice = formatCurrency($.formatCurrency.regions[curr], total_fnl.toFixed(2));
    poObj.html(formatbtmprice);
	
	
    // display default price on top
    if(finalprice == specialprice && defprice >0) {		
        var potObj = $("#productTotalDefPriceTop_"+id);
        var formatdefprice = formatCurrency($.formatCurrency.regions[curr], total_def.toFixed(2));
        potObj.html('<s>'+formatdefprice+'</s>');
        $("#defpricebox").css("display","block");
    } 
	
}

// Favorite List JSON Functions
function renderFavoriteList(jsonObj,id) {
	var dataObj = jsonObj.product_list;
	var ulObj = $("#favoriteList_"+id);		
	
	if ( dataObj.items && dataObj.items.length > 0 ) {
                var tplHTML = $("#favoriteListTitleTemplate").html();
                var re = new RegExp("%category%", "g");
                tplHTML = tplHTML.replace(re,"My Favorites");
                ulObj.append(tplHTML);
		var itemsObj = dataObj.items;
		for ( var i=0; i<itemsObj.length; i++ ) {
			var imageFound = 0;
			var tplHTML = $("#favoriteListTemplate").html();
			for (key in itemsObj[i]) {
				if ( key == "image" ) {
					var re = new RegExp("%"+key+"%", "g");
					if(itemsObj[i][key]) {
						tplHTML = tplHTML.replace(re,'<img src="'+itemsObj[i][key]+'">');
						imageFound++;
					}
				} else {
					var re = new RegExp("%"+key+"%", "g");
					tplHTML = tplHTML.replace(re,itemsObj[i][key]);
				}
			}
			
			if ( imageFound < 1 ) {
				var re = new RegExp("%image%", "g");
				tplHTML = tplHTML.replace(re,'<img src="css/images/img_holder_generic.png">');
			}
			ulObj.append(tplHTML);
		}
	}
	// no item in fav
	else {
		// empty cart message
		$("#fav-empty_"+id).css("display","block");	
	
	}
	// stop ui loader
    $(".ui-loader").css("display","none");	
}

// Home page URLs
function renderHomepageLink(id) {
	var ulObj = $("#homepage-links_"+id);
	ulObj.append('<div class="homepage-subtitle paddingtopzero"></div>');
	var HomepageLinks = {
            "Homepage":{
                "links":[
                    {
                        "title":"Recent Orders",
                        "url" : storeRootUrl + "paypalsales/order/recenttemplate"			
                    },
                    {
                        "title":"Favorites",
                        "url" : storeRootUrl + "favorite/index/viewtemplate"
                    }
                ]
            }
	}
	
	var dataObj = HomepageLinks.Homepage;	
	if ( dataObj.links && dataObj.links.length > 0 ) {
		var linksObj = dataObj.links;
		
		for ( var i=0; i<linksObj.length; i++ ) {
			var tplHTML = $("#HomepageLinksTemplate").html();
			//console.log(tplHTML);
			
			for (key in linksObj[i]) {
					var re = new RegExp("%"+key+"%", "g");
					tplHTML = tplHTML.replace(re,linksObj[i][key]);
			}			
			ulObj.append(tplHTML);
		}
	}	
}

// Featured List JSON Functions
function renderHomepageFeaturedList(jsonObj,id) {

    // render links	
    renderHomepageLink(id);
	
	
    var dataObj = jsonObj.featured_category_list;
    var ulObj = $("#feature-categories_"+id);
    ulObj.append('<div class="homepage-subtitle">Featured Categories</div>');
 
    if ( dataObj.items && dataObj.items.length > 0 ) {
        var itemsObj = dataObj.items;
        for ( var i=0; i<itemsObj.length; i++ ) {
            var imageFound = 0;
            var tplHTML = $("#HomepageFeaturedTemplate").html();
			
            for (key in itemsObj[i]) {
                if ( key == "image" ) {
                    var re = new RegExp("%"+key+"%", "g");					
                    if(itemsObj[i][key]) {
                        tplHTML = tplHTML.replace(re,'<img src="'+itemsObj[i][key]+'">');
                        imageFound++;
                    }
					
                } else {
                    var re = new RegExp("%"+key+"%", "g");
                    tplHTML = tplHTML.replace(re,itemsObj[i][key]);
                }
            }
			
            if ( imageFound < 1 ) {
                var re = new RegExp("%image%", "g");
                tplHTML = tplHTML.replace(re,'<img src="css/images/img_holder_generic.png">');
            }

            ulObj.append(tplHTML);
        }
    } 
    // stop ui loader
    $(".ui-loader").css("display","none");
}

var banneritem=0;
var maxbanneritem=10;
var loadeditem=0;
var callinit=0;
var intervalID;

// Homepage JSON Functions
function renderHomepage(jsonObj,id) {
	var dataObj = jsonObj.catalog_navigation;
	
	if ( dataObj.items && dataObj.items.length > 0 ) {
		renderHomepageBanners(dataObj.items, id);
	}
	
	
	intervalID = setInterval(carousel_status(id), 1000);	
	// call flexslider
	//initFlex(id);
}



function carousel_status(id) {
	//console.log('c banneritem='+banneritem+' loadeditem='+loadeditem);
	if(loadeditem==banneritem) {
		initFlex(id);
		clearInterval(intervalID);
		banneritem=0;
		loadeditem=0;
		//console.log('## banneritem='+banneritem+' loadeditem='+loadeditem);
	}
}

function renderHomepageBanners(itemsObj, id) {
    var liObj = $('#banner_'+id);
    //console.log(imgitem);
    for ( var i=0; i<itemsObj.length; i++ ) {
        if ( itemsObj[i].image && itemsObj[i].image != "" && banneritem < maxbanneritem) {
            //var tplHTML = '<li><a href="javascript:forwardURL(\'product-list.php?id='+itemsObj[i].id+'\')"><img src="'+itemsObj[i].image+'"></a></li>';
            var tplHTML = '<li style="width:140px"><a href="javascript:forwardURL(\'' + storeRootUrl+ '/jsoncatalog/product/listtemplate/id/'+itemsObj[i].id+'\')"><img src="'+itemsObj[i].image+'"></a></li>';
            liObj.append(tplHTML);
            banneritem++;
            loadeditem++;
        }
        if ( itemsObj[i].items && itemsObj[i].items.length > 0 && banneritem < maxbanneritem) {
            renderHomepageBanners(itemsObj[i].items, id);
        }
    //console.log(imgitem);
    }
            
}

// Product page - save favorites	
function savefav(qid) {
	
    var rqid = qid.split('_');
    sid = rqid[0];
    pid = rqid[1];  // product id
	
    // check current class
    fav = 0;	
    if($('#favorite_'+sid).hasClass('saved-fav')){
        fav =1; // current status = saved
    }

    // ## Debug ##
    //favurl1 = 'testfav2.php';
    //favurl2 = 'testfav.php';
		
    if(fav) { 
        // remove fav
        favurl = storeRootUrl +'/favorite/index/delete/product_id/'+pid;	
        $('#favorite_'+sid).removeClass('saved-fav');
        $('#favorite_'+sid).html('Save to Favorites');
		
        // submit fav
        $.ajax({   
            url: favurl,     
            success: function(response){
                var message = '';
                if (response.messages.success.length)
                    message = response.messages.success;
                else
                    message = response.messages.error;
                $('#favorite_'+sid).after("<span class='change'>"+message+"</span>");
                $("span").filter('.change').fadeOut(3000);
        		
                if(!response.success) {	// revert 
                    $('#favorite_'+sid).addClass('saved-fav');
                    $('#favorite_'+sid).html('Saved');
                }
            },  
            error: function(obj){
                $('#favorite_'+sid).after("<span class='change' style='color:red'>Error to save...</span>");
                $("span").filter('.change').fadeOut(3000);
                $('#favorite_'+sid).addClass('saved-fav');
                $('#favorite_'+sid).html('Saved');
            } 
        }); 
		
		
    }else {
        // add fav
        favurl = storeRootUrl +'/favorite/index/add/product_id/'+pid;	
        $('#favorite_'+sid).addClass('saved-fav');
        $('#favorite_'+sid).html('Saved');
		
        // submit fav
        $.ajax({   
            url: favurl,     
            success: function(response){  
                //var obj = jQuery.parseJSON(response);	// debug ##	
                var message = '';
                if (response.messages.success.length)
                    message = response.messages.success;
                else
                    message = response.messages.error;
                $('#favorite_'+sid).after("<span class='change'>"+message+"</span>");
                $("span").filter('.change').fadeOut(3000);  
	  			      	
                if(!response.success) {	// revert 
                    $('#favorite_'+sid).removeClass('saved-fav');
                    $('#favorite_'+sid).html('Save to Favorites');
                }
            },  
            error: function(response){
                $('#favorite_'+sid).after("<span class='change' style='color:red'>Error to save...</span>");
                $("span").filter('.change').fadeOut(3000);
                $('#favorite_'+sid).removeClass('saved-fav');
                $('#favorite_'+sid).html('Save to Favorites');
            } 
        }); 		
		
    }
        
    return( false );		
	
}

// Product page - add to cart
function addtocart(qid) {

    //link = "testupdatecart.php";  // test mode
    link = storeRootUrl+"jsoncheckout/cart/add";	
	
    $(".ui-loader").css("display","block"); 
        
    var form = $('#frm_'+qid);
    //console.log(form.serialize());
    $.ajax({   
        type: "POST",
        url: link,
        data: form.serialize(),  
        success: function(response){	
            //console.log(response);
            //var obj = jQuery.parseJSON(response);
            //console.log(obj);
            //console.log(obj.messages.error.length);
            if(response.messages.error.length==0) {        		
                // go to cart page again
                forwardURL(storeRootUrl + "jsoncheckout/cart/template");
            //$(".ui-loader").css("display","none");
            }  // hv error value
            else {
                $(".ui-loader").css("display","none");     
                //alert(obj.messages.error);
                var pdObj = $("#productError_"+qid).empty();
                var tplHTML = $("#ErrorPageTemplate").html();	
					
                var re = new RegExp("%error%", "g");
                tplHTML = tplHTML.replace(re,response.messages.error);
                pdObj.append(tplHTML);			
                $("#productError_"+qid).css("display","block");		
						       			
                $('.content-box').animate({
                    scrollTop: 0
                }, 0);
                return( false );
            }
        },  
        error: function(obj){	
            $(".ui-loader").css("display","none"); 
            var pdObj = $("#productError_"+qid).empty();
            var tplHTML = $("#ErrorPageTemplate").html();
			
            var re = new RegExp("%error%", "g");
            tplHTML = tplHTML.replace(re,response.messages.error);
            pdObj.append(tplHTML);			
            $("#productError_"+qid).css("display","block");		
						       			
            $('.content-box').animate({
                scrollTop: 0
            }, 0);	
            return( false );	
        } 
    }); 		
				

}

// Cart page - click on update Cart button
function updatecart(qid) {

    //link = "testupdatecart.php";  // test mode
    link = storeRootUrl+"jsoncheckout/cart/update";	
    $(".ui-loader").css("display","block"); 
    var form = $('#payform_'+qid);
    var data = form.serialize(); 
    //console.log(data);
	
    $.ajax({   
        type: "POST",
        url: link,
        data: form.serialize(),  
        success: function(obj){		
        		
            if(obj.messages.error.length==0) {     
                //$(".ui-loader").css("display","none"); 
                // go to cart page again
                forwardURL(storeRootUrl + "jsoncheckout/cart/template");
        	
            }  // hv error value
            else {
                $(".ui-loader").css("display","none");
                //alert(obj.messages.error);
                var pdObj = $("#productError_"+qid).empty();
                var tplHTML = $("#ErrorPageTemplate").html();	
					
                var re = new RegExp("%error%", "g");
                tplHTML = tplHTML.replace(re,obj.messages.error);
                pdObj.append(tplHTML);			
                $("#productError_"+qid).css("display","block");		
						       			
                $('.content-box').animate({
                    scrollTop: 0
                }, 0);
                return( false );
            }
        },  
        error: function(obj){
            $(".ui-loader").css("display","none");
            if(obj.messages.error.length==0) {        		
                // go to cart page again
                forwardURL(storeRootUrl + "jsoncheckout/cart/template");
        	
            }  // hv error value
            else {
        			
                //alert(obj.messages.error);
                var pdObj = $("#productError_"+qid).empty();
                var tplHTML = $("#ErrorPageTemplate").html();	
					
                var re = new RegExp("%error%", "g");
                tplHTML = tplHTML.replace(re,obj.messages.error);
                pdObj.append(tplHTML);			
                $("#productError_"+qid).css("display","block");		
						       			
                $('.content-box').animate({
                    scrollTop: 0
                }, 0);
                return( false );
            }        
        } 
    }); 
           	
}

// cart page - click on remove item
function deleteitem(qid) {
	
    var rqid = qid.split('_');
    sid = rqid[0];
    pid = rqid[1];  // product id
        
    //link = "testupdatecart.php";  // test mode
    link = storeRootUrl + "/jsoncheckout/cart/delete/id/"+pid;	
    $(".ui-loader").css("display","block");
    var form = $('#frm_'+qid);
	
    $.ajax({   
        url: link,  
        success: function(response){	
              		
            if(response.messages.error.length==0) {        		
                // go to cart page again
                forwardURL(storeRootUrl + "jsoncheckout/cart/template");
        	
            }  // hv error value
            else {        	
                $(".ui-loader").css("display","none");
                var pdObj = $("#productError_"+sid).empty();
                var tplHTML = $("#ErrorPageTemplate").html();	
					
                var re = new RegExp("%error%", "g");
                tplHTML = tplHTML.replace(re,response.messages.error);
                pdObj.append(tplHTML);			
                $("#productError_"+sid).css("display","block");		
						       			
                $('.content-box').animate({
                    scrollTop: 0
                }, 0);
                return( false );
            }
        },  
        error: function(obj){	
            $(".ui-loader").css("display","none");
            var pdObj = $("#productError_"+sid).empty();
            var tplHTML = $("#ErrorPageTemplate").html();
			
            var re = new RegExp("%error%", "g");
            tplHTML = tplHTML.replace(re,response.messages.error);
            pdObj.append(tplHTML);			
            $("#productError_"+sid).css("display","block");		
						       			
            $('.content-box').animate({
                scrollTop: 0
            }, 0);	
            return( false );	
        } 
    }); 		
				
}

function paynow(qid) {

    //link = "testupdatecart.php";  // test mode
    link = storeRootUrl+"paypal_checkin/index/checkout";

    $.ajax({   
        url: link,  
        success: function(obj){		
        		
            if(obj.messages.error.length==0) {     
        		   		
                // go to cart page again
                forwardURL(storeRootUrl + "jsoncheckout/onepage/successtemplate");
        	
            }  // hv error value
            else {
        			
                //alert(obj.messages.error);
                var pdObj = $("#productError_"+qid).empty();
                var tplHTML = $("#ErrorPageTemplate").html();	
					
                var re = new RegExp("%error%", "g");
                tplHTML = tplHTML.replace(re,obj.messages.error);
                pdObj.append(tplHTML);			
                $("#productError_"+qid).css("display","block");		
						       			
                $('.content-box').animate({
                    scrollTop: 0
                }, 0);
                return( false );
            }
        },  
        error: function(obj){
        	
            if(obj.messages.error.length==0) {        		
                // go to cart page again
                forwardURL(storeRootUrl + "jsoncheckout/cart/template");
        	
            }  // hv error value
            else {
        			
                //alert(obj.messages.error);
                var pdObj = $("#productError_"+qid).empty();
                var tplHTML = $("#ErrorPageTemplate").html();	
					
                var re = new RegExp("%error%", "g");
                tplHTML = tplHTML.replace(re,obj.messages.error);
                pdObj.append(tplHTML);			
                $("#productError_"+qid).css("display","block");		
						       			
                $('.content-box').animate({
                    scrollTop: 0
                }, 0);
                return( false );
            }        
        } 
    }); 
           	
}



function cartDelete(id) {
    jQuery('#btnDelete_'+id).fadeOut(100, function() {
        jQuery('#btnDelete_'+id+'_Confirm').fadeIn(100);
    });
}

function hideDeleteConfirmation() {
    jQuery('.delete-button-confirm').each(function() {
        if ( jQuery(this).css('display') == "block" ) {
            var id1 = this.id;
            var id2 = "#"+id1.replace("_Confirm","");
            jQuery(this).fadeOut(100, function() {
                jQuery(id2).fadeIn(100);
            });
        }
    });
}


// for the shopping cart icon 
function updateCartCount(num) {

    if(num !=0 ){
        $('#cartCount').addClass('cart-color');
        $('#cartCount').html(num);
    }else{
        $('#cartCount').removeClass('cart-color');
        $('#cartCount').html('');
    }
}

// Product Info
function showProductInfo(id) {
    //var topHTML = '<div class="product-info-box"><div class="cart-update button-gap-top"><a href="#" onclick="hideProductInfo()" class="secondary-button ">Done</a></div>';	
    //var bottomHTML = '<div class="cart-update button-gap-bottom"><a href="#" onclick="hideProductInfo()" class="secondary-button">Done</a></div></div>';
	
    var topHTML = '<div class="product-info-box"><div class="productinfo-title"><a href="#" onclick="hideProductInfo()">Back</a></div>';	
    var tplHTML = topHTML + $("#longDesc_"+id).html();// + bottomHTML;
	
    $("#productInfo").html(tplHTML);
    resizeContent();
    $("#productInfo").fadeIn(300);
}

function hideProductInfo() {
    $("#productInfo").fadeOut(300, function() {
        $("#productInfo").html("");
    });
}

function renderPickupTime(open_datetime,id){

    //console.log(id);
    //define device theme
    var $theme = "default";
    //if device is idevice, use ios theme
    if(/ipad|iphone|ipod/i.test(navigator.userAgent.toLowerCase())){
        $theme = "ios";
    //alert('ios');
    }
 
    //if device is android device, use android theme
    else if(/android/i.test(navigator.userAgent.toLowerCase())){
        $theme = "android-ics";
    }
 
    //if device is windows phone device, use wp theme
    else if(/windows phone/i.test(navigator.userAgent.toLowerCase())){
        $theme = "wp";
    }
 
    var $minDate = new Date();
    $minDate.setDate($minDate.getDate() + parseInt(open_datetime.mindate)); //set min date
    var $maxDate = new Date();
    $maxDate.setDate($maxDate.getDate() + parseInt(open_datetime.maxdate)); //set max date
    $date = new Date(); //reset date to today
    /*pickup time options */
    var maxdate = (open_datetime.maxdate != null)?$maxDate:null;
    var mindate = (open_datetime.mindate != null)?$minDate:null;
    var stepMinute = (open_datetime.minuteStep != null)?parseInt(open_datetime.minuteStep):1;
    var timeEnabled = parseInt(open_datetime.timeEnabled);
    
    if(!timeEnabled){
        console.log('date');
        $('#selectedDateTime_'+id).mobiscroll().date({
            theme: $theme,
            display: 'top',
            dateFormat: 'M dd, yyyy',
            timeWheels: 'HH:ii', 
            dateOrder: 'D ddmmyy',
            animate: 'none',
            maxDate: maxdate, 
            minDate: mindate, 
            onChange: __validate, 
            onShow: __validateOnShow
        });
    } else {
        $('#selectedDateTime_'+id).mobiscroll().datetime({
            theme: $theme,
            display: 'top',
            dateFormat: 'M dd, yyyy',
            timeWheels: 'HH:ii', 
            dateOrder: 'D ddmmyy',
            stepMinute: stepMinute,
            showNow: false, 
            animate: 'none',
            maxDate: maxdate, 
            minDate: mindate,
            onChange: __validate,
            onShow: __validateOnShow
        });
    }
    
    function __validate(value, inst){
    
        var date = new Date((parseInt(inst.temp[1]) + 1) + "/" + inst.temp[0] + "/" + inst.temp[2]);
    
        validatePickupTime(date, inst, open_datetime, 1, timeEnabled);
    }
 
    function __validateOnShow(html, inst){
        var set = 0;
        var date = new Date();
        if($('#selectedDateTime_'+id).val() != 'Select'){
            var date = new Date($('#selectedDateTime_'+id).val().substr(0, 10));
            set = 0;
        }
        validatePickupTime(date, inst, open_datetime, 0, timeEnabled);
    }
    
    $('#selectedDateTime_'+id).css('display', 'block');
}
 
function validatePickupTime(date, inst, open_datetime, set, timeEnabled){
    var current_in_minute = 0;
    if(timeEnabled){
        //get day time of the selected date
        if(set)
            current_in_minute = (parseInt(inst.temp[5])? 12 * 60 : 0) + parseInt(inst.temp[3]) * 60 + parseInt(inst.temp[4]);
        else
            current_in_minute = date.getHours() * 60 + date.getMinutes();
    }
    //validate selected date with exception dates
    var selected_date = date;
    selected_date.setHours(0, 0, 0, 0); //get date without day time for selected date
    var exception_open_time = new Array();
    var html = "Open"; 
    for(var i = 0; i < open_datetime.exception.length; i++){
        var exception_date = new Date(open_datetime.exception[i].day);
        if(exception_date - selected_date == 0){ //compare selected date and the exception date
            if(parseInt(open_datetime.exception[i].is_open)){
                exception_open_time.push({
                    'from_in_minute': parseInt(open_datetime.exception[i].from_hour) * 60 + parseInt(open_datetime.exception[i].from_minute),
                    'to_in_minute': parseInt(open_datetime.exception[i].to_hour) * 60 + parseInt(open_datetime.exception[i].to_minute)
                });
                html += " from " + open_datetime.exception[i].from_hour + ":" + open_datetime.exception[i].from_minute
                + " to " + open_datetime.exception[i].to_hour + ":" + open_datetime.exception[i].to_minute;
            } else  {
                document.getElementById('openinfo').innerHTML = "Closed on this day";
                $('#setBtn').css('display', 'none');
                return;
            }
        }
    }
    if(exception_open_time.length && timeEnabled){
        document.getElementById('openinfo').innerHTML = html;
        for(var j = 0; j < exception_open_time.length; j++){
            if(exception_open_time[j].from_in_minute <= current_in_minute && current_in_minute <= exception_open_time[j].to_in_minute){
                $('#setBtn').css('display', 'block');
                return;
            }
        }
        $('#setBtn').css('display', 'none');
        return;
    } else if(!timeEnabled){
        $('#setBtn').css('display', 'block');
    }
    
    //if selected date is not an exception date and validate with weekday rule
    var weekday_opentime;
    switch(date.getDay()){
        case 0:
            weekday_opentime = open_datetime.weekday_open[7];
            break;
        case 1:
            weekday_opentime = open_datetime.weekday_open[1];
            break;
        case 2:
            weekday_opentime = open_datetime.weekday_open[2];
            break;
        case 3:
            weekday_opentime = open_datetime.weekday_open[3];
            break;
        case 4:
            weekday_opentime = open_datetime.weekday_open[4];
            break;
        case 5:
            weekday_opentime = open_datetime.weekday_open[5];
            break;               
        case 6:
            weekday_opentime = open_datetime.weekday_open[6];
            break;
        default:
            break;
    }
    
    if(weekday_opentime.is_open == '1'){
        document.getElementById('openinfo').innerHTML = "Open from "
        + weekday_opentime.from_hour + ":" + weekday_opentime.from_minute
        + " to " + weekday_opentime.to_hour + ":" + weekday_opentime.to_minute;
        if(timeEnabled){
            var from_in_minute = parseInt(weekday_opentime.from_hour) * 60 + parseInt(weekday_opentime.from_minute);
            var to_in_minute = parseInt(weekday_opentime.to_hour) * 60 + parseInt(weekday_opentime.to_minute);
            if(from_in_minute <= current_in_minute && current_in_minute <= to_in_minute)
                $('#setBtn').css('display', 'block');
            else
                $('#setBtn').css('display', 'none');
        } else {
            $('#setBtn').css('display', 'block');
        }
    }
    else{
        //console.log('1799');
        document.getElementById('openinfo').innerHTML = "Closed on this day";
        $('#setBtn').css('display', 'none');
    }
}

function validate_pickuptime_and_pay(id) {
    var pickup_time = $('#selectedDateTime_' + id).val();
    if (!pickup_time) {
        forwardURL(storeRootUrl+ "/paypal_checkin/index/checkout");
        return ;
    }
    if (pickup_time == 'Select') {
        alert('Please select a valid pickup time');
        return ;
    }
    forwardURL(storeRootUrl+ "/paypal_checkin/index/checkout?pickup_time=" + pickup_time);
}
