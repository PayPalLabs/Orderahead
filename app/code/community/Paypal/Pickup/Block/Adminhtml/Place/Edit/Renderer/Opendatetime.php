<?php
/**
 * Pickup
 *
 * @package      :  Paypal_Pickup
 * @version      :  0.1.0
 * @since        :  Magento 1.7
 * @author       :  Paypal - http://www.paypal.com
 * @copyright    :  Copyright (C) 2013 Powered by Paypal
 * @license      :  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @Creation Date:  01/03/2013
 *
 */
class Paypal_Pickup_Block_Adminhtml_Place_Edit_Renderer_Opendatetime extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface
{
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        if (Mage::registry('place_data')->getOpenDatetime()) {
            $openDatetime = Mage::registry('place_data')->getOpenDatetime();
        }else {
            $openDatetime = Mage::registry('place_data')->getData('open_datetime');
        }
        $days = array(
            '1' => Mage::helper('paypal_pickup')->__('Monday'),
            '2' => Mage::helper('paypal_pickup')->__('Tuesday'),
            '3' => Mage::helper('paypal_pickup')->__('Wednesday'),
            '4' => Mage::helper('paypal_pickup')->__('Thursday'),
            '5' => Mage::helper('paypal_pickup')->__('Friday'),
            '6' => Mage::helper('paypal_pickup')->__('Saturday'),
            '7' => Mage::helper('paypal_pickup')->__('Sunday')
        );
        $minutesPeriod = Mage::helper('paypal_pickup')->getMinutesPeriod();

        $html = '<tr>';
        $html .= '
            <td class="label">
                <label for="open_datetime">' . Mage::helper('paypal_pickup')->__('Open Days/Hours') . '<span class="required"> *</span></label>
            </td>
        ';
        $html .= '
            <td class="value">
                <div class="grid form-list open-date-time">
                    <table class="border" cellspacing="0" cellpadding="0">
                        <colgroup>
                            <col width="16%"/>
                            <col width="14%"/>
                            <col width="14%"/>
                            <col width="56%"/>
                        </colgroup>
                        <tbody>
        ';

        foreach ($days as $day => $label) {
            $radioSelected = '';
            if (!empty($openDatetime[$day]['is_open'])) {
                $openSelected = 'checked="checked"';
                $closeSelected = '';
                $style = 'style="width:100%;"';
            } else {
                $openSelected = '';
                $closeSelected = 'checked="checked"';
                $style = 'style="display:none;"';
            }
            $html .= '
                <tr>
                    <td class="extra-field">
                        <label>' . $label . '</label>
                    </td>
                    <td class="is-open">
                        <input type="radio" id="open_datetime[' . $day . '][is_open]" name="open_datetime[' . $day . '][is_open]" value="1" onchange="showDatetime(this)" ' . $openSelected . '>
                        <label>' . Mage::helper('paypal_pickup')->__('Open') . '</label>
                    </td>
                    <td class="is-open">
                        <input type="radio" name="open_datetime[' . $day . '][is_open]" value="0" onchange="hideDatetime(this)" ' . $closeSelected . '>
                        <label>' . Mage::helper('paypal_pickup')->__('Close') . '</label>
                    </td>
                    <td class="open-time" ' . $style . '>
                        <div>
                            <label>' . Mage::helper('paypal_pickup')->__('From') . '</label>
                            <select id="open_datetime[' . $day . '][from_hour]" name="open_datetime[' . $day . '][from_hour]" class="select select-datetime from-hour" style="width:50px">
                                ' . $this->renderSelect('h', 0, 23, 1, empty($openDatetime[$day]['from_hour']) ? '':$openDatetime[$day]['from_hour']) . '
                            </select>
                            <label>:</label>
                            <select id="open_datetime[' . $day . '][from_minute]" name="open_datetime[' . $day . '][from_minute]" class="select select-datetime from-minute" style="width:50px">
                                ' . $this->renderSelect('m', 0, 59, $minutesPeriod, empty($openDatetime[$day]['from_minute'])? '':$openDatetime[$day]['from_minute']) . '
                            </select>
                        </div>
                        <div>
                            <label>' . Mage::helper('paypal_pickup')->__('To') . '</label>
                            <select id="open_datetime[' . $day . '][to_hour]" name="open_datetime[' . $day . '][to_hour]" class="select select-datetime to-hour" style="width:50px">
                                ' . $this->renderSelect('h', 0, 23, 1, empty($openDatetime[$day]['to_hour'])? '':$openDatetime[$day]['to_hour']) . '
                            </select>
                            <label>:</label>
                            <select id="open_datetime[' . $day . '][to_minute]" name="open_datetime[' . $day . '][to_minute]" class="select select-datetime to-minute" style="width:50px">
                                ' . $this->renderSelect('m', 0, 59, $minutesPeriod, empty($openDatetime[$day]['to_minute']) ? '' : $openDatetime[$day]['to_minute']) . '
                            </select>
                        <div>
                    </td>
                </tr>
                <tr class="validation">
                    <td colspan="3"></td>
                    <td class="value">
                        <input id="open_datetime[' . $day . '][datetime_diff]" type="hidden" class="validate-datetime"/>
                    </td>
                </tr>
            ';
        }

        $html .= '</tbody></table></div></td></tr>';
        $html .= '
        <script type="text/javascript">
            Validation.addAllThese([
                ["validate-min-days","' . Mage::helper('paypal_pickup')->__('Min Days must not be greater than Max Days.') . '", function(v) {
                    return (parseInt(v) < parseInt($("datetime_max_days").getValue()));
                }],
                ["validate-max-days","' . Mage::helper('paypal_pickup')->__('Max Days must not be less than Min Days.') . '", function(v) {
                    return (parseInt(v) > parseInt($("datetime_min_days").getValue()));
                }],
                ["validate-datetime","' . Mage::helper('paypal_pickup')->__('To time must be greater than From time.') . '", function(v, elm) {
                    var tmp = elm.id;
                    tmp = tmp.substring(0, tmp.indexOf("]") + 1);
                    var isOpen = tmp + "[is_open]";
                    if ($(isOpen)) {
                        return (!((parseInt(v) <= 0) && $(isOpen).checked));
                    }
                    return false;
                }],
                ["validate-datetime-ex","' . Mage::helper('paypal_pickup')->__('To time must be greater than From time.') . '", function(v, elm) {
                    var tmp = elm.id;
                    tmp = tmp.substring(0, tmp.indexOf("-"));
                    var isOpen = tmp + "_is_open0";
                    if ($(isOpen)) {
                        return ((parseInt(v) > 0) || $(isOpen).checked);
                    }
                    return false;
                }],
            ]);
            document.observe("dom:loaded", function () {
                $$(".validate-datetime").each(function(el) {
                    var tmp = el.getAttribute("id");
                    tmp = tmp.substring(0, tmp.indexOf("]") + 1);
                    var fromHour = tmp + "[from_hour]";
                    var fromMinute = tmp + "[from_minute]";
                    var toHour = tmp + "[to_hour]";
                    var toMinute = tmp + "[to_minute]";
                    var datetimeDiff = parseInt($(toHour).getValue()) * 60 + parseInt($(toMinute).getValue()) - (parseInt($(fromHour).getValue()) * 60 + parseInt($(fromMinute).getValue()));
                    el.setValue(datetimeDiff);
                });
                $$(".select-datetime").each(function(el) {
                    var tmp = el.getAttribute("id");
                    tmp = tmp.substring(0, tmp.indexOf("]") + 1);
                    var fromHour = tmp + "[from_hour]";
                    var fromMinute = tmp + "[from_minute]";
                    var toHour = tmp + "[to_hour]";
                    var toMinute = tmp + "[to_minute]";
                    var datetimeDiffElm = tmp + "[datetime_diff]";
                    el.observe("change", function () {
                        if ($(fromHour) && $(fromMinute) && $(toHour) && $(toMinute)) {
                            var datetimeDiff = parseInt($(toHour).getValue()) * 60 + parseInt($(toMinute).getValue()) - (parseInt($(fromHour).getValue()) * 60 + parseInt($(fromMinute).getValue()));
                            $(datetimeDiffElm).setValue(datetimeDiff);
                        }
                    });
                });
            });
            function showDatetime(el) {
                if (el.checked) {
                    if (el.up("tr") && el.up("tr").down("td.open-time")) {
                        el.up("tr").down("td.open-time").show();
                    }
                }
            }

            function hideDatetime(el) {
                if (el.checked) {
                    if (el.up("tr") && el.up("tr").down("td.open-time")) {
                        el.up("tr").down("td.open-time").hide();
                    }
                }
            }
        </script>
        ';

        return $html;
    }

    public function renderSelect($type, $from, $to, $interval, $current)
    {

        if ($type == 'm' && !is_numeric($interval)) {
            $interval = 15;
        }
        if ($type == 'h' && !is_numeric($interval)) {
            $interval = 1;
        }
        $html = '';
        for ($i = 0; $from + $i * $interval <= $to; $i++) {
            $selected = '';
            if (($from + $i * $interval) == $current) {
                $selected = 'selected="selected"';
            }
            $optionLabel = $from + $i * $interval;
            if (($from + $i * $interval == 0) && ($type == 'm')) {
                $optionLabel = '00';
            }
            $html .= '<option value="' . ($from + $i * $interval) . '" ' . $selected . '>' . $optionLabel . '</option>';
        }

        return $html;
    }
}

