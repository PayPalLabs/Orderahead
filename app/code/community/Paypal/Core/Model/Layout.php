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
class Paypal_Core_Model_Layout extends Mage_Core_Model_Layout
{

    /**
     * Layout Update module
     *
     * @var Mage_Core_Model_Layout_Update
     */
    protected $_update;

    /**
     * Blocks registry
     *
     * @var array
     */
    protected $_blocks = array();

    /**
     * Cache of block callbacks to output during rendering
     *
     * @var array
     */
    protected $_output = array();

    /**
     * Layout area (f.e. admin, frontend)
     *
     * @var string
     */
    protected $_area;

    /**
     * Helper blocks cache for this layout
     *
     * @var array
     */
    protected $_helpers = array();

    /**
     * Flag to have blocks' output go directly to browser as oppose to return result
     *
     * @var boolean
     */
    protected $_directOutput = false;

    /**
     * Get all blocks marked for output
     *
     * @return string
     */
    public function getArrayOutput()
    {
    	$out = array();
    	if (!empty($this->_output)) {
    		foreach ($this->_output as $callback) if ($callback[0] !== 'root') {
    			$out[$callback[0]]= Mage::helper('core')->jsonDecode($this->getBlock($callback[0])->$callback[1]());
    		}
    	}
    
    	return $out;
    }
}
