/**
 * @author : Everett Quebral <equebral@paypal.com>
 * @requires : ppbridge.js <https://github.paypal.com/equebral/MerchantHybridPrototype/blob/master/public/js/ppbridge.js>
 *
 * The Contract for Merchant to interact with the PayPal Client App
 * 
 * How To Use
 * 
 *  1.  Include this javascript into your page, after including the ppbridge.js
 *		<script src="ppbridge.js"></script>
 *		<script src="generic-merchant-core.js"></script>  <!-- this file -->
 *	2.  On the DOMReady Event, execute PayPalApp.call("MerchantTitleBar")
 *		this will set the Title Bar and the Left Button (Back) in the PayPal Client App
 * 	
 */
var PayPalApp = $.NativeBridge,
merchantConfig = {
	actions : {
		/**
		 * action for setting the TitleBar, Left Button (Back), the Right Button is disregarded because the PayPal Client App controls it
		 * when the back button is clicked, the PayPal Client App will call handler tag 3
		 */
		"MerchantTitleBar" : {
			func : "SetTitleBar",
			args : {
				WindowTitle : window.document.title,
				LeftButton : {
					text : "Back",
					type : "BACK",
					tag  : 3
				}
			}
		},
		/**
		 * action for setting the TitleBar, Left Button (Back), Right Button is disregarded because the PayPal Client App controls it.
		 * when the back button is clicked, tha PayPal Client App will call handler tag 1
		 */
		"MerchantTitleBarBackToPP" : {
			func : "SetTitleBar",
			args : {
				WindowTitle : window.document.title,
				LeftButton : {
					text : "Back",
					type : "BACK",
					tag  : 1
				}
			}
		},
		/**
		 * action for setting the TitleBar, Left Button (Back)
		 * additional setting is applied to the back button as it is disabled, it is being shown in PayPal Client App but not clickable
		 */
		"MerchantTitleBarDisabledBack" : {
			func : "SetTitleBar",
			args : {
				WindowTitle : window.document.title,
				LeftButton : {
					text : "Back",
					type : "BACK",
					style : {disabled:true},
					tag  : 1
				}
			}
		}
	},
	handlers : {
		1 : function(e){
			PayPalApp.call({func:"DismissWebView"});
			return true;
		},
		3 : function(e){
		 	window.history.back();
		 	return true;
		}
	},
	callbacks : {}
};

// Set the MerchantTitleBar when the DOM is ready or the content is loaded
// this is the native implementation of the item number 2 in the How To Use section, simply uncomment the line below.
// You can also call PayPalApp.call({ActionID}) anywhere in your script for your specific needs

document.addEventListener("DOMContentLoaded", function(){
	// Initialize the actions, handlers and callbacks
	PayPalApp.init(merchantConfig); 
	PayPalApp.call("MerchantTitleBar");
}, false);

