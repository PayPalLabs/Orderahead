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
class Paypal_Instorepickupadmin_Block_Adminhtml_Page_Toolbar extends Mage_Adminhtml_Block_Widget_Container
{
    protected $_title = '';
    protected $_isShowHomeButton = true;

    /**
     * Get Title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->_title;
    }

    /**
     * Set section id
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
     * Get Title
     *
     * @return string
     */
    public function getIsShowHomeButton()
    {
        return $this->_isShowHomeButton;
    }

    /**
     * Set section id
     *
     * @param string
     * @return Paypal_Instorepickupadmin_Block_Adminhtml_Page_Toolbar
     */
    public function setIsShowHomeButton($isShow)
    {
        $this->_isShowHomeButton = $isShow;
        return $this;
    }

    /**
     * Return Home Page Url
     *
     * @return Paypal_Instorepickupadmin_Block_Adminhtml_Section
     */
    public function getHomeUrl()
    {
        return Mage::getUrl('adminhtml/instorepickupadmin_order');
    }

    /**
     * Return Home Page Url
     *
     * @return string
     */
    public function getLogoutUrl()
    {
        return Mage::getUrl('adminhtml/instorepickupadmin_order/logout');
    }
}
