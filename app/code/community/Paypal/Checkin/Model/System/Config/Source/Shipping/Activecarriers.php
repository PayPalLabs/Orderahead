<?php
class Paypal_Checkin_Model_System_Config_Source_Shipping_Activecarriers
{
	/**
	 * Return array of carriers.
	 * If $isActiveOnlyFlag is set to true, will return only active carriers
	 *
	 * @return array
	 */
	public function toOptionArray()
	{
		//$methods = array(array('value'=>'','label'=>Mage::helper('adminhtml')->__('--Please Select--')));
 
		$methods = array();
		
        $activeCarriers = Mage::getSingleton('shipping/config')->getActiveCarriers();
        foreach($activeCarriers as $carrierCode => $carrierModel)
        {
           $options = array();
           if( $carrierMethods = $carrierModel->getAllowedMethods() )
           {
               foreach ($carrierMethods as $methodCode => $method)
               {
                   $code= $carrierCode.'_'.$methodCode;
                   $options[]=array('value'=>$code,'label'=>$method);
 
               }
               $carrierTitle = Mage::getStoreConfig('carriers/'.$carrierCode.'/title');
 
           }
           if(is_array($options) && count($options) > 0)
            $methods[]=array('value'=>$options,'label'=>$carrierTitle);
        }
        //Mage::log(var_export($methods, true));
   		return $methods;
	}
}