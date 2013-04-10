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
 * @Creation Date:  12/03/2013
 *
 */
class Paypal_Pickup_Block_Adminhtml_Place_Edit_Renderer_Storehourexceptions extends Varien_Data_Form_Element_Abstract
{
    public function __construct($attributes = array())
    {
        parent::__construct($attributes);

    }

    public function getRenderer()
    {
        $this->_renderer = new Paypal_Pickup_Block_Adminhtml_Place_Edit_Renderer_StorehourexceptionsRender();
        return $this->_renderer;
    }

    public function getElementHtml()
    {
        $html = $this->getRenderer()->render($this);
        return $html;
    }
}