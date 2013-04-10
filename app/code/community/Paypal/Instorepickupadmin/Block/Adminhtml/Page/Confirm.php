<?php
/**
 * Instore Pickup Admin
 *
 * @package      :  Paypal_Instorepickupadmin
 * @version      :  0.1.0
 * @since        :  Magento 1.7
 * @author       :  Paypal - http://www.paypal.com
 * @copyright    :  Copyright (C) 2013 Powered by Paypal
 * @license      :  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @Creation Date:  01/03/2013
 *
 */
class Paypal_Instorepickupadmin_Block_Adminhtml_Page_Confirm extends Mage_Adminhtml_Block_Template
{
    protected $_title = '';
    protected $_message = '';
    protected $_buttons = array();
    protected $_prefixId = 'confirm_popup_';
    protected $_id = '';

    public function __construct(){
        parent::__construct();
        $this->setTemplate('paypal/instorepickupadmin/page/confirm.phtml');
    }

    /**
     * Get popup id
     *
     * @return string
     */
    public function getId()
    {
        return $this->_prefixId.$this->_id;
    }

    /**
     * Set id for confirm popup
     *
     * @param string $id
     * @return Paypal_Instorepickupadmin_Block_Adminhtml_Page_Confirm
     */
    public function setId($id)
    {
        $this->_id = $id;
        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->_title;
    }

    /**
     * Set title for confirm popup
     *
     * @param string
     * @return Paypal_Instorepickupadmin_Block_Adminhtml_Page_Toolbar
     */
    public function setTitle($title)
    {
        $this->_title = $title;
        return $this;
    }

    /**
     * Get Message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->_message;
    }

    /**
     * Set message
     *
     * @param string
     * @return Paypal_Instorepickupadmin_Block_Adminhtml_Page_Toolbar
     */
    public function setMessage($message)
    {
        $this->_message = $message;
        return $this;
    }

    /**
     * Add button
     *
     * @param array $button
     * @return Paypal_Instorepickupadmin_Block_Adminhtml_Page_Toolbar
     */
    public function addButton($button)
    {
        $defaultAttributes = array(
            "data-inline"  =>  "true",
            "data-theme"=>"c"
        );
        $attributes = array();
        // set default attribute
        if (isset($button['attributes'])) {
            $attributes = array_merge($defaultAttributes,$button['attributes']);
        } else {
            $attributes = $defaultAttributes;
        }
        $attributesHtml = '';
        foreach($attributes as $attrName => $attrValue){
            $attributesHtml .= $attrName . '="'.$attrValue.'" ';
        }
        $button = array(
            'title' => $button['title'],
            'attributes' => $attributesHtml
        );
        $this->_buttons[] = $button;
        return $this;
    }

    /**
     * Get Message
     *
     * @return string
     */
    public function getButtons()
    {
        return $this->_buttons;
    }
}
