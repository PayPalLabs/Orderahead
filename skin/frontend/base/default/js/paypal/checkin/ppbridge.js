/**
 * PayPal Native Bridge Library
 * @author : Everett Quebral
 * @version : 3.0.2
 *
 * This is the stand alone (meaning no library required) version of the PayPal Native Bridge for communicating
 * with the native app and with the web app
 *
 * Base version that doesn't have some of the functionalities of the core since 
 * this will only be used for external integration
 * 
 * This is the version the external merchants will use
 * 
 * To use, just call $.NativeBridge.init(args);  // args is the native contract object
 */

/*jslint nomen: true, debug: true, evil: false, vars: true */

(function(x){
	"use strict";
	
	x.PPBridge = function(){
		var actions = {},
			handlers = {},
			callbacks = {},
			actionCount = 0,
			callbacksCount = 1,
			options = {},
			self,
			wait = 0,
			commandQueue = [],
			timeoutValue = 100,
			timeout,
			bridgeInitTimeout = 25,
			ready = false,
			
			/**
			 * prepare all the variables and configuration for the bridge
			 * 	@param
			 * 		args {string|object}
			 * 				if string, this is the action id	
			 *				else, this is the option object
			 */
			prepare = function(args){
				var actionId,
					hasCallback,
					callbackId;
				
				if (typeof args === "string"){
					actionId = args;
					args = actions[actionId];
				}
				else if (typeof args === "object"){
					actionId = actionCount++;
					actions[actionId] = args;
				}
				
				options.actionId = actionId;
				options.functionName = args.func;
				if (args.args){
					options.callback = args.args.cb || "";
				}
				options.config = args.args || "";
				
				hasCallback = options.callback && typeof options.callback === "function";
				callbackId = hasCallback ? callbacksCount++ : 0;
				
				if (hasCallback){
					callbacks[callbackId] = options.callback;
					actions[actionId]["cb"] = callbackId;
				}
				else {
					callbackId = options.callback;
				}
				
				return ({
						"callbackId" : callbackId, 
						"options" : options,
						"actionId" : actionId
					});
			};
			


		return {
			/**
			 * initialize all arguments
			 *  @param
			 *		args {object}
			 */
			init : function(args){
				//console.log("PPBridge - init ", args);
				function _init(){
					if (args){
						actions = args.actions;
						handlers = args.handlers;
						callbacks = args.callbacks;
					}

					function methodCall(func, actionId){
						window.location = "jsr://" + func + "/" + actionId;
					}

					// do this only with the PayPal Client
					if ( window.ppIOS ){
						ppIOS.methodCall = methodCall;
					}
					// we are now ready for executing bridge calls
					ready = true;
				}

				self = this;
				setTimeout(function(){
					console.log("initializing bridge");
					_init();
				}, bridgeInitTimeout);
			},
			
			/**
			 * the native calls this method to pass the result back
			 * to the web and will take the result and execute the callback function
			 *  @params
			 * 		callbackId 	{string} 	the id of the callback
			 * 		result 		{object}	object literal for the result
			 */
			getResult : function(callbackId, result){
				//callbacks[callbackId] && callbacks[callbackId].apply(callbacks[callbackId], [result]);
				
				var callback = callbacks[callbackId];
				if (!callback) return;
				
				callback.apply(callback, [result]);
			},
			
			/**
			 * the client calls this method to handle
			 * control from the buttons.
			 * 	@param
			 * 		handlerTag {number}	the tag for the action handler
			 *
			 *  @return 
			 *		true if the handler is executed
			 *		false if the handler is not found in the handler table
			 */
			callHandler : function(handlerTag){
				//handlers[handlerTag] && handlers[handlerTag].apply(handlers[handlerTag], []);
				
				var handler = handlers[handlerTag],
					returnValue = "";
				if (!handler) return 'false';
				
				// when the handler is called, the client native app 
				// can check whether the callHandler executed or not
				// with our implementation the client will expect 'true' to be returned

				returnValue = handler.apply(handler, []);
				if ((typeof returnValue === "undefined") || returnValue){
					return 'true';
				}
				else {
					return 'false';
				}
			},			
			
			/**
			 * the native code will get the action based from the id sent through the 
			 * url protocol -> jsr://Action/actionId
			 *  @params
			 *		actionId {string}	the id of the action
			 *  @return
			 *		string representation of the action object
			 */
			getAction : function( actionId ){
				if( !actionId || actionId == "*" ) return JSON.stringify(actions);
				var action = actions[actionId];
				if( !action ) return;
				return JSON.stringify(action);
			},
			
			/**
			 * call the NativeBridge to perform the native to web communication
			 * 	@param
			 *		arguments	{object}			the arguments that make up the communication request
			 */
			call : function( args ) {
				//console.log("PPBridge - call ", args);
				function _execute(args){
					var executeParam = prepare(args);
					self.execute(executeParam.callbackId, executeParam.options);
				};

				if (!ready){
					//console.log("bridge is not ready, calling it again in 25ms");
					setTimeout(function(){
						self.call(args);
					}, bridgeInitTimeout);
					return false;
				}
				
				if (wait){
					//console.log("please wait pushing to queue");
					commandQueue.push(args);
					
					// double check the commandQueue specially in the interval to make
					// sure we are not making multiple instances of the interval
					if (commandQueue.length > 0){
						clearInterval(timeout);
						timeout = setInterval(function(){
							if (commandQueue.length > 0){
								//console.log("executing ... in queue");
								_execute(commandQueue.shift());
							}
							else {
								//console.log("clearing interval");
								clearInterval(timeout);
							}
						}, timeoutValue);
					}

					return false;
				}
				else {	
					wait = 1;
					setTimeout(function(){
						//console.log("clearing wait flag");
						wait = 0;
					},timeoutValue);
					_execute(args);
				}
			},
			
			execute : function(callbackId, options){
				if (window.ppAndroid && window.ppAndroid.methodCall){
					ppAndroid.methodCall(options.functionName, JSON.stringify(options.config));
				}
				else if ( window.ppIOS && window.ppIOS.methodCall ){
					ppIOS.methodCall(options.functionName, options.actionId);
				}
				else {
					try {
						window.external.notify(JSON.stringify({func:options.functionName, args:options.config}));
					}
					catch (e) {
						console.log(e);
					}
				}
			}
		};
	}();

	// for backwards compatibility with the client app because it is using $.NativeBridge as the reference
	// preserve $ because the client might be using jQuery
	if (x.$){
		x.$.NativeBridge = x.PPBridge;
	}
	else {
		x.$ = {
			NativeBridge : x.PPBridge
		};
	}
}(window));

