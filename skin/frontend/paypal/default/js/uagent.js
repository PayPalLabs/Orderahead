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
document.write('<meta id="metaViewport" name="viewport" content="">');
var uagent = navigator.userAgent;
var viewportString = "";
if ( uagent.indexOf("iPhone") >= 0 || uagent.indexOf("iPod") >= 0 ) {
	// iPhones & iPods
	viewportString = "width=320, maximum-scale=1.0, user-scalable=no";
} else if ( uagent.indexOf("iPad") >= 0 ) {
	// iPad
	viewportString = "width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no";
} else if ( uagent.indexOf("Android") >= 0 ) {
	// Android Devices
	viewportString = "width=device-width, maximum-scale=1.0, user-scalable=0";
} else {
	// Desktop & Other Tablets
	viewportString = "width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no";
}
var viewportObj = document.getElementById("metaViewport");
viewportObj.setAttribute("content",viewportString);
