<?php
/**
 * Pickup
 *
 * @package      :  Paypal_Locationhere
 * @version      :  0.9.0
 * @since        :  Magento 1.7
 * @author       :  Paypal - http://www.paypal.com
 * @copyright    :  Copyright (C) 2013 Powered by Paypal
 * @license      :  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @Creation Date:  01/03/2013
 *
 */
?>
<?php
class Paypal_Locationhere_Block_Edit extends Paypal_Pickup_Block_Adminhtml_Place_Edit_Tabs_Form
{
    protected function _initFormValues(){
        $form = $this->getForm();
        $placeData = Mage::registry('place_data');
        /*
         * Add PayPal Here API checkbox 
         */
        $fieldset = $form->addFieldset('paypal_here_api_form', array('legend' => Mage::helper('paypal_pickup')->__('PayPal Here API')));

        $fieldset->addField('paypal_here_api_enabled', 'select', array(
            'label' => Mage::helper('paypal_pickup')->__('Enabled'),
            'name' => 'paypal_here_api_enabled',
            'values' => array(
                array(
                    'value' => 1,
                    'label' => Mage::helper('paypal_pickup')->__('Yes'),
                ),
                array(
                    'value' => 0,
                    'label' => Mage::helper('paypal_pickup')->__('No'),
                ),
            ),
        ));
        
        $storeViewArray = array();
        $storeViews = Mage::getModel('core/store')->getCollection();
        foreach($storeViews as $storeView){
            $storeViewArray[] = array(
                'value' => $storeView->getStoreId(), 
                'label' => $storeView->getName()
            );
        }
        
        $fieldset->addField('paypal_here_store_view', 'select', array(
            'label' => Mage::helper('paypal_pickup')->__('Store View'), 
            'name' => 'paypal_here_store_view', 
            'values' => $storeViewArray
        ));
        
        
        $form->setValues($placeData);
        
        $this->setForm($form);
        return $this;
    }
}

