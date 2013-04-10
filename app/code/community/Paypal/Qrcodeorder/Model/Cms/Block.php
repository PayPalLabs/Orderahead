<?php
/**
 * Qrcodeorder
 *
 * @package      :  Paypal_Qrcodeorder
 * @version      :  0.9.0
 * @since        :  Magento 1.7
 * @author       :  Paypal - http://www.paypal.com
 * @copyright    :  Copyright (C) 2013 Powered by Paypal
 * @license      :  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @Creation Date:  19/03/2013
 * 
 */
class Paypal_Qrcodeorder_Model_Cms_Block extends Mage_Cms_Model_Block
{
    public function toOptionArray() {
        $config_data = Mage::getSingleton('adminhtml/config_data')->getData();

        $blocks = $this->getCollection();
        if ($config_data['scope'] === 'stores') {
            $blocks->addStoreFilter($config_data['scope_id']);
        }
        else if ($config_data['scope'] === 'websites') {
            $website = Mage::app()->getWebsite($config_data['scope_id']);
            $stores = $website->getStores();
            $store_ids = array();
            foreach($stores as $store) {
                $store_ids[] = $store->getId();
            }
            $blocks->addStoreFilter($store_ids);
        }
        else if ($config_data['scope'] === 'default') {
        }

        $result = array(array(
            'value' => 0,
            'label' => 'No static block',
        ));
        foreach($blocks as $block) {
            $result[] = array(
                'value' => $block->getId(),
                'label' => $block->getTitle(),
            );
        }
        return $result;
    }
}
