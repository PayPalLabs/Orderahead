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

/*Instore script*/

function renderPlace(el, placeId) {
    $$('div.place-content').each(function (elm) {
        elm.hide();
    });
    $$('input[name="place-id"]')[0].setValue(placeId);
    // Show Loading
    var Loader = $('shipping-method-please-wait-' + placeId);
    if (Loader) {
        Loader.show();
    }
    new Ajax.Request(PAYPAL_BASE_URL + 'paypal_pickup/place/ajax', {
        method:'get',
        parameters:{id:placeId},
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
                var placeContent = 'place-content-' + placeId;
                if ($(placeContent)) {
                    $(placeContent).update(response.html);
                    $(placeContent).show();
                    $(placeContent).addClassName('active');
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
            // Hide Loading
            if (Loader) {
                Loader.hide();
            }
        }
    });
}
function renderHourSelector(el, placeId, flag, saveFlag) {
    $$('select.place-date').each(function (elm) {
        Event.observe(elm, 'change', function () {
            $$('input[name="place-date"]')[0].setValue(elm.getValue());
        });
        if (elm.up('.place-content').hasClassName('active')) {
            $$('input[name="place-date"]')[0].setValue(elm.getValue());
        }
    });
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
}

function renderMinuteSelector(el, placeId, saveFlag) {
    $$('select.place-hour').each(function (elm) {
        Event.observe(elm, 'change', function () {
            $$('input[name="place-hour"]')[0].setValue(elm.getValue());
        });
        if (elm.up('.place-content').hasClassName('active')) {
            $$('input[name="place-hour"]')[0].setValue(elm.getValue());
        }
    });
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
                }
            }
        }
    }
}

function updatePlaceMinute(el, placeId) {
    $$('input[name="place-minute"]')[0].setValue(el.getValue());
}

function updatePlaceDurationsOptions(el, placeId) {
    $$('input[name="place-durations-options"]')[0].setValue(el.getValue());
}


function clearSelect(el) {
    for (var i = el.options.length - 1; i >= 0; i--) {
        el.remove(i);
    }
}

/*Instore script*/