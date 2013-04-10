<?php
/**
 * PayPal Core
 *
 * @package      :  PayPal_Core
 * @version      :  0.9.0
 * @since        :  Magento 1.7
 * @author       :  Paypal - http://www.paypal.com
 * @copyright    :  Copyright (C) 2013 Powered by Paypal
 * @license      :  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @Creation Date:  19/03/2013
 * 
 */
abstract class Paypal_Core_Controller_Front_Action extends Mage_Core_Controller_Front_Action {

	/**
	 * Rendering layout in JSON
	 *
	 * @param   string $output
	 * @return  Mage_Core_Controller_Varien_Action
	 */
	public function renderJsonLayout($output='')
	{
		$_profilerKey = self::PROFILER_KEY . '::' . $this->getFullActionName();
	
		if ($this->getFlag('', 'no-renderLayout')) {
			return;
		}
	
		if (Mage::app()->getFrontController()->getNoRender()) {
			return;
		}
	
		Varien_Profiler::start("$_profilerKey::layout_render");
	
	
		if (''!==$output) {
			$this->getLayout()->addOutputBlock($output);
		}
	
		Mage::dispatchEvent('controller_action_layout_render_before');
		Mage::dispatchEvent('controller_action_layout_render_before_'.$this->getFullActionName());

		#ob_implicit_flush();
		$this->getLayout()->setDirectOutput(false);
	
		$output = $this->getLayout()->getArrayOutput();
        $this->addSessionMessages($output);
		$jsonOutput = Mage::helper('core')->jsonEncode($output);
		
		//Mage::getSingleton('core/translate_inline')->processResponseBody($output);
	
		// $code = 404;
		$code = 200;
		$this->getResponse()->setHttpResponseCode($code);
		$this->getResponse()->setHeader('Content-type', 'application/json');

		$this->getResponse()->setBody($jsonOutput);
	
		Varien_Profiler::stop("$_profilerKey::layout_render");
	
		return $this;
	}

    /**
     * Add messages & errors from session to json output
     *
     * @param array $jsonOutput
     */
    public function addSessionMessages(& $output) {
        $block = $this->getLayout()->getMessagesBlock();
        $messages = $block->getMessages();
        $output['messages'] = array(
            'error' => array(),
            'success' => array(),
            'notice' => array(),
        );
        foreach($messages as $message) {
            $type = $message->getType();
            $output['messages'][$type][] = $message->getText();
        }
    }
	
	/**
     * Retrieve current layout object
     *
     * @return Paypal_Core_Model_Layout
     */
    public function getLayout()
    {
        return Mage::getSingleton('paypal_core/layout');
    }

    /**
     * Load layout by handles(s)
     *
     * @param   string|null|bool $handles
     * @param   bool $generateBlocks
     * @param   bool $generateXml
     * @return  Mage_Core_Controller_Varien_Action
     */
    /*
    public function loadLayout($handles = null, $generateBlocks = true, $generateXml = true)
    {
    	// if handles were specified in arguments load them first
    	if (false!==$handles && ''!==$handles) {
    		$this->getLayout()->getUpdate()->addHandle($handles ? $handles : 'default');
    	}
    
    	// add default layout handles for this action
    	$this->addActionLayoutHandles();
    
    	$this->loadLayoutUpdates();
        
    	if (!$generateXml) {
    		return $this;
    	}
    	$this->generateLayoutXml();
        
    	if (!$generateBlocks) {
    		return $this;
    	}
    	$this->generateLayoutBlocks();
    	$this->_isLayoutLoaded = true;
    
    	return $this;
    }
     * 
     */
}
