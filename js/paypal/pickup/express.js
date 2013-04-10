/**
 * Pickup
 *
 * @package      :  Paypal_Pickup
 * @version      :  0.1.0
 * @since        :  Magento 1.7
 * @author       :  Paypal - http://www.paypal.com
 * @copyright    :  Copyright (C) 2013 Powered by Paypal
 * @license      :  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @Creation Date:  12/03/2013
 *
 */

document.observe("dom:loaded", function () {
    if($('review_button') && PayPalExpressAjax){
        $('review_button').stopObserving('click');
        Event.observe($('review_button'), 'click', function(){
            //for magento CE
            if($('shipping_method_form')){
                expressShippingForm = new VarienForm('shipping_method_form', true);
                if (expressShippingForm.validator.validate()) {
                    PayPalExpressAjax._submitOrder();
                }
            }
            else if($('shipping-method-container')){
                expressShippingForm = new VarienForm('shipping-method-container', true);
                if (expressShippingForm.validator.validate()) {
                    PayPalExpressAjax._submitOrder();
                }
            }
            else{
                PayPalExpressAjax._submitOrder();
            }
        });
    }


    if ($("shipping_method")) {
        var shippingCode = $("shipping_method").getValue();
        var placeCode = shippingCode.substring(8);
        $("shipping_method").observe('change', function () {
            var shippingCode1 = $("shipping_method").getValue();
            var placeCode1 = shippingCode1.substring(8);
            // Create ajax request to customize instore shipping method
            if (shippingCode1.indexOf('instore') >= 0) {
                renderPlace(placeCode1);
            }else {
                if ($("shipping_method").next(1)) {
                    $("shipping_method").next(1).update('');
                }
            }
        });
        if (shippingCode.indexOf('instore') >= 0) {
            renderPlace(placeCode);
        }
    }

    if(typeof(PayPalExpressAjax._updateShipping) !='undefined'){
        PayPalExpressAjax._updateShipping = function () {
            if ($(this.shippingSelect)) {
                $(this.shippingSelect).enable();
                Event.stopObserving($(this.shippingSelect), 'change');

                this._bindElementChange($(this.shippingSelect));
                Event.observe(
                    $(this.shippingSelect),
                    'change',
                    this._submitUpdateOrder.bindAsEventListener(this, this._submitUpdateOrderUrl, this._itemsGrid)
                );

                $(this.shippingSelect + '_update').hide();
                $(this.shippingSelect).show();
            }
            this._updateShippingMethods = false;
            if (this._pleaseWait) {
                this._pleaseWait.hide();
            }

            if ($("shipping_method")) {
                var shippingCode = $("shipping_method").getValue();
                var placeCode = shippingCode.substring(8);
                $("shipping_method").observe('change', function () {
                    var shippingCode1 = $("shipping_method").getValue();
                    var placeCode1 = shippingCode1.substring(8);
                    // Create ajax request to customize instore shipping method
                    if (shippingCode1.indexOf('instore') >= 0) {
                        renderPlace(placeCode1);
                    }else {
                        if ($("shipping_method").next(1)) {
                            $("shipping_method").next(1).update('');
                        }
                    }
                });
                if (shippingCode.indexOf('instore') >= 0) {
                    renderPlace(placeCode);
                }
            }
        }
    }
});

dateConfig = {};

function renderPlace(placeCode) {
    var placeCode = placeCode;
    var placeContent = $("shipping_method").up('fieldset');
    if (placeContent.down('.place')) {
        placeContent.down('.place').hide();
    }

    if ($('instore-loader') == null) {
        var Loader = document.createElement('span');
        Loader.setAttribute('id', 'instore-loader');
        var ImgLoader = document.createElement('img');
        ImgLoader.setAttribute('src', PAYPAL_LOADING_IMG_URL);
        var LabelLoader = document.createElement('span');
        LabelLoader.update('Loading');
        Loader.appendChild(ImgLoader);
        Loader.appendChild(LabelLoader);
        $(placeContent).appendChild(Loader);
    }
    if ($('instore-loader')) {
        $('instore-loader').show();
    }
    new Ajax.Request(PAYPAL_BASE_URL + 'paypal_pickup/place/reviewAjax', {
        method:'get',
        parameters:{place_code:placeCode},
        onSuccess:function (transport) {
            if (transport && transport.responseText) {
                try {
                    response = eval('(' + transport.responseText + ')');
                }
                catch (e) {
                    response = {};
                }
            }
            // Update place content
            if (response.html != '') {

                var placeId = response.place_id;

                if ($(placeContent) && placeId) {
                    $$("fieldset div.place").each(function (el) {
                        el.remove();
                    });
                    var div = document.createElement("div");
                    div.setAttribute('class', 'place');
                    $(placeContent).appendChild(div);
                    $(div).update(response.html);
                    // Update instore information from quote
                    if (response.type == "1") {
                        if (response.place_durations_options !== null) {
                            if ($('durations-options-select-' + placeId)) {
                                updateSelected($('durations-options-select-' + placeId), response.place_durations_options);
                            }
                        }
                    } else {
                        if (response.place_date !== null) {
                            if ($('date-select-' + placeId)) {
                                updateSelected($('date-select-' + placeId), response.place_date);

                            }
                        }
                        if (response.place_hour !== null) {
                            if ($('hour-select-' + placeId)) {
                                setTimeout(function () {
                                    renderHourSelector($('date-select-' + placeId), placeId, true, false);
                                    updateSelected($('hour-select-' + placeId), response.place_hour);
                                }, 500);
                            }
                        }
                        if (response.place_minute !== null) {
                            if ($('minute-select-' + placeId)) {
                                setTimeout(function () {
                                    renderMinuteSelector($('hour-select-' + placeId), placeId, false);
                                    updateSelected($('minute-select-' + placeId), response.place_minute);
                                }, 500);
                            }
                        }
                    }

                    // Show hour and minutes select
                    var placeHour = 'place-hour-' + placeId;
                    var placeMinute = 'place-minute-' + placeId;
                    if ($(placeHour)) {
                        $(placeHour).show();
                    }
                    if ($(placeMinute)) {
                        $(placeMinute).show();
                    }
                }
            }
            if ($('instore-loader')) {
                $('instore-loader').hide();
            }
        }
    });
}

function renderHourSelector(el, placeId, flag, saveFlag) {
    if (flag) {
        // Clear and init hour and minute select
        var hourSelector = $('hour-select-' + placeId);
        if (hourSelector) {
            clearSelect(hourSelector);
            // Init select
            hourSelector.options[0] = new Option('--', '');
        }
        var minuteSelector = $('minute-select-' + placeId);
        if (minuteSelector) {
            clearSelect(minuteSelector);
            // Init select
            minuteSelector.options[0] = new Option('--', '');
        }
        if (typeof dateConfig[placeId] != 'undefined') {
            var placeHourConfig = dateConfig[placeId][el.getValue()];
            if (typeof(placeHourConfig) != 'undefined') {
                if (hourSelector) {
                    // Update select
                    var index = 1;
                    for (var hour in placeHourConfig) {
                        if (!isNaN(hour)) {
                            hourSelector.options[index] = new Option(hour, hour);
                            index++;
                        }
                    }
                    // Show hour select
                    var parentHour = $('place-hour-' + placeId);
                    if (parentHour) {
                        parentHour.show();
                    }
                }
            }
        }
    }
    if (saveFlag) {
        saveInstoreInformation(placeId);
    }
}

function renderMinuteSelector(el, placeId, saveFlag) {
    var minuteSelector = $('minute-select-' + placeId);
    // Clear minute select
    if (minuteSelector) {
        clearSelect(minuteSelector);
        // Init select
        minuteSelector.options[0] = new Option('--', '');
    }
    var dateSelector = $('date-select-' + placeId);
    if (typeof dateConfig[placeId] != 'undefined') {
        if (typeof dateConfig[placeId][dateSelector.getValue()] != 'undefined') {
            var placeMinuteConfig = dateConfig[placeId][dateSelector.getValue()][el.getValue()];
            if (typeof(placeMinuteConfig) != 'undefined') {
                if (minuteSelector) {
                    // Update select
                    for (var i = 0; i < placeMinuteConfig.length; i++) {
                        var optionLabel = placeMinuteConfig[i];
                        if (placeMinuteConfig[i] == 0) {
                            optionLabel = '00';
                        }
                        minuteSelector.options[i + 1] = new Option(optionLabel, placeMinuteConfig[i]);
                    }
                    // Show minute select
                    var parentMinute = $('place-minute-' + placeId);
                    if (parentMinute) {
                        parentMinute.show();
                    }
                    if (saveFlag) {
                        saveInstoreInformation(placeId);
                    }
                }
            }
        }
    }
}

function saveInstoreInformation(placeId) {
    // Prepare data for ajax request
    var placeDate = '';
    var placeHour = '';
    var placeMinute = '';
    var placeDurationsOptions = '';
    if ($('date-select-' + placeId)) {
        placeDate = $('date-select-' + placeId).getValue();
    }
    if ($('hour-select-' + placeId)) {
        placeHour = $('hour-select-' + placeId).getValue();
    }
    if ($('minute-select-' + placeId)) {
        placeMinute = $('minute-select-' + placeId).getValue();
    }
    if ($('durations-options-select-' + placeId)) {
        placeDurationsOptions = $('durations-options-select-' + placeId).getValue();
    }
    // Create ajax request to save instore information
    new Ajax.Request(PAYPAL_BASE_URL + 'paypal_pickup/place/saveInstoreAjax', {
        method:'get',
        parameters:{
            place_id:placeId,
            place_date:placeDate,
            place_hour:placeHour,
            place_minute:placeMinute,
            place_durations_options:placeDurationsOptions
        },
        onSuccess:function (transport) {
        }
    });
}

function updatePlaceMinute(el, placeId) {
    $$('select.place-minute')[0].setValue(el.getValue());
    saveInstoreInformation(placeId);
}

function updatePlaceDurationsOptions(el, placeId) {
    $$('select.place-durations-options')[0].setValue(el.getValue());
    saveInstoreInformation(placeId);
}


function clearSelect(el) {
    for (var i = el.options.length - 1; i >= 0; i--) {
        el.remove(i);
    }
}

function updateSelected(el, val) {
    for (var i = 0; i < el.options.length; i++) {
        if (el.options[i].value == val)
            el.selectedIndex = i;
    }

}
