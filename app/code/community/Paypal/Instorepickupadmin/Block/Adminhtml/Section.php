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
class Paypal_Instorepickupadmin_Block_Adminhtml_Section extends Mage_Adminhtml_Block_Widget_Container
{
    protected $_sectionId = '';
    protected $_sectionTitle = '';
    protected $_confirmPopups = array();
    /**
     * Get Section Id
     *
     * @return string
     */
    public function getSectionId()
    {
        return $this->_sectionId;
    }

    /**
     * Set section id
     *
     * @param string $sectionId
     * @return Paypal_Instorepickupadmin_Block_Adminhtml_Section
     */
    public function setSectionId($sectionId)
    {
        $this->_sectionId = $sectionId;
        return $this;
    }

    /**
     * Get Section Title
     *
     * @return string
     */
    public function getSectionTitle()
    {
        return $this->_sectionTitle;
    }

    /**
     * Set section id
     *
     * @param string $sectionTitle
     * @return Paypal_Instorepickupadmin_Block_Adminhtml_Section
     */
    public function setSectionTitle($sectionTitle)
    {
        $this->_sectionTitle = $sectionTitle;
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
     * @return Paypal_Instorepickupadmin_Block_Adminhtml_Section
     */
    public function getLogoutUrl()
    {
        return Mage::getUrl('adminhtml/instorepickupadmin_order/logout');
    }

    public function addConfirmPopup($popup){
        $this->_confirmPopups[] = $popup;
    }

    protected function _beforeToHtml(){

        if(!empty($this->_confirmPopups)){
            foreach($this->_confirmPopups as $popup){
                $confirmPopup = $this->getLayout()->createBlock('paypal_instorepickupadmin/adminhtml_page_confirm');
                if(!empty($popup['id'])){
                    $confirmPopup->setId($popup['id']);
                }

                if(!empty($popup['title'])){
                    $confirmPopup->setTitle($popup['title']);
                }

                if(!empty($popup['message'])){
                    $confirmPopup->setMessage($popup['message']);
                }

                if(!empty($popup['buttons'])){
                    foreach($popup['buttons'] as $button){
                        $confirmPopup->addButton($button);
                    }
                }
                if(!empty($popup['name'])){
                    $this->getLayout()->getBlock('footer')->append($confirmPopup,$popup['name']);
                }
            }
        }
        return parent::_beforeToHtml();
    }
}
