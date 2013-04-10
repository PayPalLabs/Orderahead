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
class Paypal_Pickup_Block_Adminhtml_Place_Edit_Tabs_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $placeData = Mage::registry('place_data');

        $fieldset = $form->addFieldset('place_form', array('legend' => Mage::helper('paypal_pickup')->__('Place Information')));

        $placeCode = $fieldset->addField('place_code', 'text', array(
            'label' => Mage::helper('paypal_pickup')->__('Place Code'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'place_code',
        ));
        if ($placeData->getData('place_id')) {
            $placeCode->setDisabled('disabled');
        }

        $fieldset->addField('place_name', 'text', array(
            'label' => Mage::helper('paypal_pickup')->__('Place Name'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'place_name',
        ));

        $fieldset->addField('status', 'select', array(
            'label' => Mage::helper('paypal_pickup')->__('Status'),
            'name' => 'status',
            'values' => array(
                array(
                    'value' => 1,
                    'label' => Mage::helper('paypal_pickup')->__('Enabled'),
                ),
                array(
                    'value' => 0,
                    'label' => Mage::helper('paypal_pickup')->__('Disabled'),
                ),
            ),
        ));

        $fieldset->addField('description', 'textarea', array(
            'label' => Mage::helper('paypal_pickup')->__('Description'),
            'name' => 'description',
        ));

        $openDatetime = $fieldset->addField('open_datetime', 'text', array(
            'label' => Mage::helper('paypal_pickup')->__('Open Days/Hours'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'open_datetime',
        ));
        $openDatetime->setRenderer(Mage::app()->getLayout()->createBlock(
            'paypal_pickup/adminhtml_place_edit_renderer_opendatetime'
        ));

        $fieldset = $form->addFieldset('place_address_form', array('legend' => Mage::helper('paypal_pickup')->__('Address Information')));

        $fieldset->addField('address', 'text', array(
            'label' => Mage::helper('paypal_pickup')->__('Address'),
            'name' => 'address',
            'class' => 'required-entry',
            'required' => true,
        ));

        $fieldset->addField('city', 'text', array(
            'label' => Mage::helper('paypal_pickup')->__('City'),
            'name' => 'city',
            'class' => 'required-entry',
            'required' => true,
        ));

        $country = $fieldset->addField('country', 'select', array(
            'label' => Mage::helper('paypal_pickup')->__('Country'),
            'name'  => 'country',
            'class' => 'required-entry',
            'required' => true,
        ));
        $country->setRenderer(Mage::app()->getLayout()->createBlock(
            'paypal_pickup/adminhtml_place_edit_renderer_country'
        ));

        $state = $fieldset->addField('region_id', 'select', array(
            'label' => Mage::helper('paypal_pickup')->__('State/Province'),
            'name'  => 'region_id',
        ));
        $state->setRenderer(Mage::app()->getLayout()->createBlock(
            'paypal_pickup/adminhtml_place_edit_renderer_region'
        ));

        $fieldset->addField('postcode', 'text', array(
            'label' => Mage::helper('paypal_pickup')->__('Postal Code'),
            'name' => 'postcode',
            'class' => 'required-entry',
            'required' => true,
        ));

        $fieldset->addField('longitude', 'text', array(
            'label' => Mage::helper('paypal_pickup')->__('Longitude'),
            'name' => 'longitude',
        ));

        $fieldset->addField('latitude', 'text', array(
            'label' => Mage::helper('paypal_pickup')->__('Latitude'),
            'name' => 'latitude',
        ));

        $fieldset->addField('phone', 'text', array(
            'label' => Mage::helper('paypal_pickup')->__('Phone'),
            'name' => 'phone',
            'class' => 'required-entry ',
            'required' => true,
        ));

        $fieldset->addField('email', 'text', array(
            'label' => Mage::helper('paypal_pickup')->__('Email'),
            'name' => 'email',
            'class' => 'required-entry validate-email',
            'required' => true,
        ));

        $state = $fieldset->addField('timezone', 'select', array(
            'label' => Mage::helper('paypal_pickup')->__('Timezone'),
            'name'  => 'timezone',
            'values'=> Mage::getModel('adminhtml/system_config_source_locale_timezone')->toOptionArray()
        ));

        /**
         * Check is single store mode
         */
        if (!Mage::app()->isSingleStoreMode()) {
            $fieldset->addField('store_id', 'multiselect', array(
                'name'      => 'stores[]',
                'label'     => Mage::helper('cms')->__('Store View'),
                'title'     => Mage::helper('cms')->__('Store View'),
                'required'  => true,
                'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
            ));
        }
        else {
            $fieldset->addField('store_id', 'hidden', array(
                'name'      => 'stores[]',
                'value'     => Mage::app()->getStore(true)->getId()
            ));
            $placeData->setStoreId(Mage::app()->getStore(true)->getId());
        }

        if (Mage::getSingleton('adminhtml/session')->getPlaceData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getPlaceData());
            Mage::getSingleton('adminhtml/session')->setPlaceData(null);
        } elseif ($placeData) {
            $data = $placeData->getData();
            if(!empty($data['stores'])){
                $storesData = implode(",", $data['stores']);
                if (!array_key_exists('store_id',$data)) {
                    $data['store_id'] = $storesData;
                }
            }

            if ($placeId = $this->getRequest()->getParam('id')) {
                $placeCode = Mage::getSingleton('paypal_pickup/place')->load($placeId)->getPlaceCode();
                $data['place_code'] = $placeCode;
                $form->getElement('place_code')->setDisabled('disabled');
            }
            $form->setValues($data);
        }

        return parent::_prepareForm();
    }
}