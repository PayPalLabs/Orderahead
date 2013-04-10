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
class Paypal_Pickup_Block_Adminhtml_Place_Edit_Tabs_Ordertime extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareLayout()
    {
        $form = new Varien_Data_Form();
        $htmlIdPrefix = '';
        $model = Mage::registry('place_data');
        $data = array();
        if (Mage::getSingleton('adminhtml/session')->getPlaceData()) {
            $data = Mage::getSingleton('adminhtml/session')->getPlaceData();
            Mage::getSingleton('adminhtml/session')->setPlaceData(null);
        } elseif ($model) {
            $data = $model->getData();
        }
        unset($data['store_hour_exceptions']);

        $fieldset = $form->addFieldset('ordertime_form', array('legend' => Mage::helper('paypal_pickup')->__('Order Fulfillment Time Configuration')));

        $fieldset->addField('allow_pickup_datetime', 'select', array(
            'label' => Mage::helper('paypal_pickup')->__('Allow Pickup Datetime'),
            'name' => 'allow_pickup_datetime',
            'values' => array(
                array(
                    'value' => '0',
                    'label' => Mage::helper('paypal_pickup')->__('Disabled'),
                ),
                array(
                    'value' => '1',
                    'label' => Mage::helper('paypal_pickup')->__('Enabled'),
                ),
            )
        ));

        $fieldset->addField('type', 'select', array(
            'label' => Mage::helper('paypal_pickup')->__('Type'),
            'name' => 'type',
            'values' => array(
                array(
                    'value' => '0',
                    'label' => Mage::helper('paypal_pickup')->__('Specify Date/Time'),
                ),
                array(
                    'value' => '1',
                    'label' => Mage::helper('paypal_pickup')->__('Specify Duration'),
                ),
            ),
            'note' => '<ul><li><small>' . Mage::helper('paypal_pickup')->__('[Type – Specify Date] will allow consumers to choose from a date/time selector during checkout.') . '</small></li>' . '<li><small>' . Mage::helper('paypal_pickup')->__('[Type – Specify Duration] will allow consumers to choose durations from the current time.') . '</small></li></ul>',
        ));

        $fieldset->addField('durations_options', 'text', array(
            'label' => Mage::helper('paypal_pickup')->__('Durations Options'),
            'name' => 'durations_options',
        ));

        $fieldset->addField('datetime_min_days', 'text', array(
            'label' => Mage::helper('paypal_pickup')->__('Min Days'),
            'name' => 'datetime_min_days',
            'class' => 'validate-zero-or-greater validate-min-days',
        ));

        $fieldset->addField('datetime_max_days', 'text', array(
            'label' => Mage::helper('paypal_pickup')->__('Max Days'),
            'name' => 'datetime_max_days',
            'class' => 'validate-zero-or-greater validate-max-days',
        ));

        $fieldset->addField('datetime_enable_time_selector', 'select', array(
            'label' => Mage::helper('paypal_pickup')->__('Enable Time Selector'),
            'name' => 'datetime_enable_time_selector',
            'values' => array(
                array(
                    'value' => '0',
                    'label' => Mage::helper('paypal_pickup')->__('No'),
                ),
                array(
                    'value' => '1',
                    'label' => Mage::helper('paypal_pickup')->__('Yes'),
                ),
            ),
        ));

        $fieldset->addField('datetime_minutes_displayed', 'text', array(
            'label' => Mage::helper('paypal_pickup')->__('Minutes To Be Displayed'),
            'name' => 'datetime_minutes_displayed',
            'class' => 'validate-minutes-displayed',
        ));

        $form->setValues($data);
        $this->setForm($form);

        $fieldset->addType('exception_options','Paypal_Pickup_Block_Adminhtml_Place_Edit_Renderer_Storehourexceptions');
        $exValue = array();
        if ($model->getStoreExceptionConfig()) {
            $exValue = $model->getStoreExceptionConfig();
        }else {
            $exValueTmp = $model->getData('store_hour_exceptions');
            if(is_array($exValueTmp)){
                if ($exValueTmp) {

                    foreach ($exValueTmp as $key => $value) {
                        if ($value != '') {
                            $exValue[$key] = $value;
                        }
                    }
                }
            }
        }
        $fieldset->addField('store_hour_exceptions', 'exception_options', array(
            'label' => Mage::helper('paypal_pickup')->__('Store Hour Exceptions'),
            'name' => 'store_hour_exceptions',
            'value' => $exValue,
        ));

        // Define field dependencies
        $this->setChild('form_after', $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
                ->addFieldMap("{$htmlIdPrefix}allow_pickup_datetime", 'allow_pickup_datetime')
                ->addFieldMap("{$htmlIdPrefix}type", 'type')
                ->addFieldMap("{$htmlIdPrefix}store_hour_exceptions", 'store_hour_exceptions')
                ->addFieldMap("{$htmlIdPrefix}durations_options", 'durations_options')
                ->addFieldMap("{$htmlIdPrefix}datetime_min_days", 'datetime_min_days')
                ->addFieldMap("{$htmlIdPrefix}datetime_max_days", 'datetime_max_days')
                ->addFieldMap("{$htmlIdPrefix}datetime_enable_time_selector", 'datetime_enable_time_selector')
                ->addFieldMap("{$htmlIdPrefix}datetime_minutes_displayed", 'datetime_minutes_displayed')
                ->addFieldDependence('type', 'allow_pickup_datetime', '1')
                ->addFieldDependence('store_hour_exceptions', 'allow_pickup_datetime', '1')
                ->addFieldDependence('datetime_min_days', 'allow_pickup_datetime', '1')
                ->addFieldDependence('datetime_max_days', 'allow_pickup_datetime', '1')
                ->addFieldDependence('datetime_enable_time_selector', 'allow_pickup_datetime', '1')
                ->addFieldDependence('datetime_minutes_displayed', 'allow_pickup_datetime', '1')
                ->addFieldDependence('durations_options', 'allow_pickup_datetime', '1')
                ->addFieldDependence('datetime_min_days', 'type', '0')
                ->addFieldDependence('datetime_max_days', 'type', '0')
                ->addFieldDependence('datetime_enable_time_selector', 'type', '0')
                ->addFieldDependence('datetime_minutes_displayed', 'type', '0')
                ->addFieldDependence('datetime_minutes_displayed', 'datetime_enable_time_selector', '1')
                ->addFieldDependence('durations_options', 'type', '1')
        );

        return parent::_prepareLayout();
    }
}