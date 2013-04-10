<?php
/**
 * Featuredcategory
 *
 * @package      :  Paypal_Featuredcategory
 * @version      :  0.9.0
 * @since        :  Magento 1.7
 * @author       :  Paypal - http://www.paypal.com
 * @copyright    :  Copyright (C) 2013 Powered by Paypal
 * @license      :  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @Creation Date:  19/03/2013
 * 
 */

$installer = $this;
$installer->startSetup();

$entityTypeId     = $installer->getEntityTypeId('catalog_category');
//$attributeSetId   = $installer->getDefaultAttributeSetId($entityTypeId);
//$attributeGroupId = $installer->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);



$installer->addAttribute('catalog_category', 'is_featured', array(
		'group'         	=> 'General Information',
		'input'         	=> 'select',
		'type'          	=> 'int',
		'source'            => 'eav/entity_attribute_source_boolean',
		'label'         	=> 'Featured',
		'global'            => 1,
        'visible'           => 1,
        'required'          => 0,
        'user_defined'      => 1,
        'searchable'        => 1,
        'filterable'        => 1,
        'comparable'        => 0,
        'visible_on_front'  => 1,
        'visible_in_advanced_search' => 0,
        'unique'            => 0,
        'is_configurable'   => 0,
        'position'          => 1
));

$installer->endSetup();
